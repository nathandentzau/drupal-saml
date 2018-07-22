<?php

namespace Drupal\saml\Event;

use Symfony\Component\EventDispatcher\Event;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\IdentityProviderInterface;

/**
 * Provides an event for receiving SAML responses.
 */
class ReceiveSamlMessageEvent extends Event {

  /**
   * Event machine name.
   */
  const NAME = 'saml.receive_response';

  /**
   * SAML message context.
   *
   * @var LightSaml\Context\Profile\MessageContext
   */
  protected $context;

  /**
   * Identity provider entity.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * Constructor for SamlResponseEvent.
   *
   * @param LightSaml\Context\Profile\MessageContext $context
   *   SAML message context.
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   Identity provider entity.
   */
  public function __construct(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $this->context = $context;
    $this->identityProvider = $identityProvider;
  }

  /**
   * Get the SAML message context.
   *
   * @return LightSaml\Context\Profile\MessageContext
   *   The SAML message context.
   */
  public function getContext() {
    return $this->context;
  }

  /**
   * Get the Identity Provider.
   *
   * @return Drupal\saml\Entity\IdentityProviderInterface
   *   The current Identity Provider.
   */
  public function getIdentityProvider() {
    return $this->identityProvider;
  }

}
