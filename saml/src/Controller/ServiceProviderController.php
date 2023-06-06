<?php

namespace Drupal\saml\Controller;

use Drupal\Core\Url;
use LightSaml\SamlConstants;
use LightSaml\Binding\BindingFactory;
use Drupal\saml\Event\ProvisionUserEvent;
use Drupal\Core\Controller\ControllerBase;
use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;
use Drupal\externalauth\ExternalAuthInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\saml\Event\RedirectLocationAlterEvent;
use LightSaml\Model\Context\SerializationContext;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use LightSaml\Model\Protocol\Response as SamlResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\saml\HttpFoundation\ExternalRedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Drupal\saml\Factory\Model\Metadata\SamlMetadataFactoryInterface;

/**
 * Provides a controller for service provider routes.
 */
class ServiceProviderController extends ControllerBase {

  /**
   * The event dispatcher.
   *
   * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The external auth service.
   *
   * @var Drupal\externalauth\ExternalAuthInterface
   */
  protected $externalAuth;

  /**
   * The SAML response message factory.
   *
   * @var Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface
   */
  protected $responseFactory;

  /**
   * The SAML authn request message factory.
   *
   * @var Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface
   */
  protected $authnRequestFactory;

  /**
   * The SAML provider metadata factory.
   *
   * @var Drupal\saml\Factory\Model\Metadata\SamlMetadataFactoryInterface
   */
  protected $metadataFactory;

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
   *   The event dispatcher.
   * @param Drupal\externalauth\ExternalAuthInterface $externalAuth
   *   The external auth service.
   * @param Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface $responseFactory
   *   The SAML response message factory.
   * @param Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface $authnRequestFactory
   *   The SAML authn request message factory.
   * @param Drupal\saml\Factory\Model\Metadata\SamlMetadataFactoryInterface $metadataFactory
   *   The SAML provider metadata factory.
   * @param Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   The page cache kill switch
   */
  public function __construct(
    EventDispatcherInterface $eventDispatcher,
    ExternalAuthInterface $externalAuth,
    SamlMessageFactoryInterface $responseFactory,
    SamlMessageFactoryInterface $authnRequestFactory,
    SamlMetadataFactoryInterface $metadataFactory,
    KillSwitch $killSwitch
  ) {
    $this->eventDispatcher = $eventDispatcher;
    $this->externalAuth = $externalAuth;
    $this->responseFactory = $responseFactory;
    $this->authnRequestFactory = $authnRequestFactory;
    $this->metadataFactory = $metadataFactory;
    $this->killSwitch = $killSwitch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher'),
      $container->get('externalauth.externalauth'),
      $container->get('saml.response_factory'),
      $container->get('saml.authn_request_factory'),
      $container->get('saml.service_provider.metadata_factory'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * SAML 2.0 Assertion Consumer Service
   *
   * SAML Response messages are to be sent to this controller method via the
   * HTTP-Post binding.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $serviceProvider
   *   The service provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\RedirectResponse
   *   Symfony redirect response.
   *
   * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   */
  public function consume(
    SamlProviderInterface $serviceProvider,
    Request $request
  ) {
    $message = $this->responseFactory->createFromRequest(
      $serviceProvider,
      $request
    );

    if (!$message instanceof SamlResponse) {
      throw new BadRequestHttpException('Message is not a Response');
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
        $serviceProvider->id(),
        [
          'mail' => $message
            ->getFirstAssertion()
            ->getFirstAttributeStatement()
            ->getFirstAttributeByName($serviceProvider->getEmailAttribute())
            ->getFirstAttributeValue(),
        ]
      );

    $this
      ->eventDispatcher
      ->dispatch(
        new ProvisionUserEvent($account, $message, $serviceProvider)
      );

    $this->killSwitch->trigger();

    $event = new RedirectLocationAlterEvent(
      $serviceProvider,
      Url::fromRoute('<front>')
    );
    $this
      ->eventDispatcher
      ->dispatch($event);

    return new RedirectResponse($event->getLocation()->toString());
  }

  /**
   * SAML 2.0 Authn Request
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $serviceProvider
   *   The service provider entity.
   *
   * @return Drupal\saml\HttpFoundation\ExternalRedirectResponse
   *   An external redirect response to the Identity Provider.
   */
  public function login(SamlProviderInterface $serviceProvider) {
    $message = $this->authnRequestFactory->create($serviceProvider);

    $bindingFactory = new BindingFactory();
    $redirectBinding = $bindingFactory->create(
      SamlConstants::BINDING_SAML2_HTTP_REDIRECT
    );

    $messageContext = new MessageContext();
    $messageContext->setMessage($message);

    $this->killSwitch->trigger();

    $redirectResponse = $redirectBinding->send($messageContext);
    return ExternalRedirectResponse::createFromRedirectResponse(
      $redirectResponse
    );
  }

  /**
   * SAML 2.0 Metadata
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $serviceProvider
   *   The service provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\Response
   *   The Service Provider metadata file outputted as XML.
   */
  public function metadata(
    SamlProviderInterface $serviceProvider,
    Request $request
  ) {
    $entityDescriptor = $this->metadataFactory->create($serviceProvider);

    $serializationContext = new SerializationContext();
    $document = $serializationContext->getDocument();
    $document->formatOutput = TRUE;

    $entityDescriptor->serialize($document, $serializationContext);

    return Response::create(
      $document->saveXML(),
      Response::HTTP_OK,
      ['Content-type' => 'text/xml']
    )->prepare($request);
  }

}
