<?php

namespace Drupal\saml\Event;

use Symfony\Component\EventDispatcher\Event;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides an event for receiving SAML responses.
 */
class ReceiveSamlMessageEvent extends Event {

  /**
   * SAML message context.
   *
   * @var LightSaml\Context\Profile\MessageContext
   */
  protected $context;

  /**
   * Service provider entity.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $serviceProvider;

  /**
   * Constructor for SamlResponseEvent.
   *
   * @param LightSaml\Context\Profile\MessageContext $context
   *   SAML message context.
   * @param Drupal\saml\Entity\SamlProviderInterface $serviceProvider
   *   Service provider entity.
   */
  public function __construct(
    MessageContext $context,
    SamlProviderInterface $serviceProvider
  ) {
    $this->context = $context;
    $this->serviceProvider = $serviceProvider;
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
   * Get the Service Provider.
   *
   * @return Drupal\saml\Entity\SamlProviderInterface
   *   The current Service Provider.
   */
  public function getServiceProvider() {
    return $this->serviceProvider;
  }

}
