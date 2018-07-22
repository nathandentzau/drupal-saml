<?php

namespace Drupal\saml\Controller;

use Drupal\Core\Url;
use Drupal\saml\Event\ProvisionUserEvent;
use Drupal\Core\Controller\ControllerBase;
use Drupal\saml\Event\ProvisionSamlUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Drupal\externalauth\ExternalAuthInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Event\RedirectLocationAlterEvent;
use LightSaml\Model\Context\SerializationContext;
use Drupal\saml\Exception\SamlValidationException;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use LightSaml\Model\Protocol\Response as SamlResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Drupal\saml\Factory\Model\Metadata\InboundConnectionMetadataFactory;

/**
 * Provides a controller for service provider routes.
 */
class InboundConnectionController extends ControllerBase {

  /**
   * Symfony event dispatcher.
   *
   * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Drupal external auth service.
   *
   * @var Drupal\externalauth\ExternalAuthInterface
   */
  protected $externalAuth;

  /**
   * SAML entity descriptor factory.
   *
   * @var Drupal\saml\Factory\Model\Metadata\InboundConnectionMetadataFactory
   */
  protected $metadataFactory;

  /**
   * Saml message factory.
   *
   * @var Drupal\saml\SamlMessageFactory
   */
  protected $messageFactory;

  /**
   * The page cache kill switch
   *
   * @var Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Constructor for ServiceProviderController.
   *
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   Symfony event dispatcher.
   * @param Drupal\externalauth\ExternalAuthInterface $externalAuth
   *   Drupal external auth service.
   * @param Drupal\saml\Factory\Model\Metadata\InboundConnectionMetadataFactory $metadataFactory
   *   Metadata factory.
   * @param Drupal\saml\SamlMessageFactory $messageFactory
   *   Saml message factory.
   * @param Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   Page cache kill switch.
   */
  public function __construct(
    EventDispatcherInterface $eventDispatcher,
    ExternalAuthInterface $externalAuth,
    InboundConnectionMetadataFactory $metadataFactory,
    SamlMessageFactoryInterface $messageFactory,
    KillSwitch $killSwitch
  ) {
    $this->eventDispatcher = $eventDispatcher;
    $this->externalAuth = $externalAuth;
    $this->metadataFactory = $metadataFactory;
    $this->messageFactory = $messageFactory;
    $this->killSwitch = $killSwitch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher'),
      $container->get('externalauth.externalauth'),
      $container->get('saml.inbound.metadata_factory'),
      $container->get('saml.response_factory'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * SAML 2.0 Assertion Consumer Service
   *
   * SAML Response messages are to be sent to this controller method via the
   * HTTP-Post binding.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The identity provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\RedirectResponse
   *   Symfony redirect response.
   *
   * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   */
  public function consume(
    IdentityProviderInterface $identityProvider,
    Request $request
  ) {
    try {
      $message = $this
        ->messageFactory
        ->createFromRequest(
          $identityProvider,
          $request
        );

      if (!$message instanceof SamlResponse) {
        throw new \Exception('Message is not a Response');
      }

      $subjectNameId = $message
        ->getFirstAssertion()
        ->getSubject()
        ->getNameId()
        ->getValue();

      $account = $this
        ->externalAuth
        ->loginRegister(
          $subjectNameId,
          $identityProvider->id(),
          [
            'mail' => $message
              ->getFirstAssertion()
              ->getFirstAttributeStatement()
              ->getFirstAttributeByName($identityProvider->getMailAttribute())
              ->getFirstAttributeValue(),
          ]
        );

      $this
        ->eventDispatcher
        ->dispatch(
          ProvisionSamlUserEvent::NAME,
          new ProvisionSamlUserEvent($account, $message, $identityProvider)
        );
    }
    catch (SamlValidationException $e) {
      throw new BadRequestHttpException(
        sprintf('SAML validation error: %s', $e->getMessage())
      );
    }

    $this->killSwitch->trigger();

    $event = new RedirectLocationAlterEvent(
      $identityProvider,
      Url::fromRoute('<front>')
    );
    $this
      ->eventDispatcher
      ->dispatch(RedirectLocationAlterEvent::class, $event);

    return new RedirectResponse($event->getLocation()->toString());
  }

  public function login() {

  }

  /**
   * SAML 2.0 Service Provider Metadata
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The identity provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\Response
   *   A XML metadata file.
   */
  public function metadata(
    IdentityProviderInterface $identityProvider,
    Request $request
  ) {
    $entityDescriptor = $this
      ->entityDescriptorFactory
      ->createServiceProvider($identityProvider);

    $serializationContext = new SerializationContext();
    $document = $serializationContext->getDocument();
    $document->formatOutput = TRUE;
    $entityDescriptor->serialize($document, $serializationContext);

    return Response::create(
      $document->samlXML(),
      Response::HTTP_OK,
      ['Content-type' => 'text/xml']
    )->prepare($request);
  }

}
