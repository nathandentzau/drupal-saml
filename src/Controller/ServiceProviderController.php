<?php

namespace Drupal\saml\Controller;

use Drupal\saml\SamlMessageFactory;
use Drupal\saml\Event\UserProvisionEvent;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\externalauth\ExternalAuthInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Model\Protocol\Response as SamlResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a controller for service provider routes.
 */
class ServiceProviderController extends ControllerBase {

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
   * Saml message factory.
   *
   * @var Drupal\saml\SamlMessageFactory
   */
  protected $messageFactory;

  /**
   * Constructor for ServiceProviderController.
   *
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   Symfony event dispatcher.
   * @param Drupal\externalauth\ExternalAuthInterface $externalAuth
   *   Drupal external auth service.
   * @param Drupal\saml\SamlMessageFactory $messageFactory
   *   Saml message factory.
   */
  public function __construct(
    EventDispatcherInterface $eventDispatcher,
    ExternalAuthInterface $externalAuth,
    SamlMessageFactory $messageFactory
  ) {
    $this->eventDispatcher = $eventDispatcher;
    $this->externalAuth = $externalAuth;
    $this->messageFactory = $messageFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher'),
      $container->get('externalauth.externalauth'),
      $container->get('saml.message_factory')
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
   */
  public function consume(
    IdentityProviderInterface $identityProvider,
    Request $request
  ) {
    try {
      $message = \Drupal::service('saml.message_factory')
        ->createFromRequest(
          $identityProvider,
          $request
        );

      if (!$message instanceof SamlResponse) {
        throw new \Exception('Message is not a Response');
      }

      $account = \Drupal::service('externalauth.externalauth')
        ->loginRegister(
          $message->getFirstAssertion()->getSubject()->getNameId()->getValue(),
          $identityProvider->id(),
          [
            'mail' => $message
              ->getFirstAssertion()
              ->getFirstAttributeStatement()
              ->getFirstAttributeByName($identityProvider->getMailAttribute())
              ->getFirstAttributeValue(),
          ]
        );

      \Drupal::service('event_dispatcher')
        ->dispatch(
          UserProvisionEvent::NAME,
          new UserProvisionEvent($account, $message, $identityProvider)
        );
    }
    catch (SamlValidationException $e) {
      return new Response($e->getMessage());
    }
    catch (\Exception $e) {
      return new Response($e->getMessage());
    }

    return new RedirectResponse('/');
  }

}
