<?php

namespace Drupal\saml\Validation;

use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Event\ReceiveSamlMessageEvent;
use Drupal\saml\Entity\IdentityProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * SAML message validator base.
 */
abstract class ValidatorBase implements EventSubscriberInterface {

  /**
   * Handle receiving a SAML message.
   *
   * @param Drupal\saml\Event\ReceiveSamlMessageEvent $event
   *   An event object when receiving saml messages.
   */
  public function onReceiveMessage(ReceiveSamlMessageEvent $event) {
    $context = $event->getContext();
    $identityProvider = $event->getIdentityProvider();

    $this->validate($context, $identityProvider);
  }

  /**
   * Validate the SAML message being received.
   *
   * @param LightSaml\Context\Profile\MessageContext $context
   *   The SAML message context.
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The Identity Provider entity.
   */
  abstract protected function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  );

  /**
   * Get the priority of this validator.
   *
   * @return int
   *   The priority of the validator.
   */
  protected static function getPriority() {
    return 100;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ReceiveSamlMessageEvent::NAME => ['onReceiveMessage', static::getPriority()],
    ];
  }

}
