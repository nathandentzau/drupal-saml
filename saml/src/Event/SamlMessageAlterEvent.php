<?php

namespace Drupal\saml\Event;

use Symfony\Component\EventDispatcher\Event;
use LightSaml\Model\Protocol\SamlMessage;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides a SAML message alter event.
 */
class SamlMessageAlterEvent extends Event {

  /**
   * The SAML message.
   *
   * @var LightSaml\Model\Protocol\SamlMessage
   */
  protected $message;

  /**
   * The SAML provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $samlProvider;

  /**
   * Provides a SamlMessageAlterEvent.
   *
   * @param LightSaml\Model\Protocol\SamlMessage $message
   *   The SAML message.
   * @param Drupal\saml\Entity\SamlProviderInterface $samlProvider
   *   The SAML provider.
   */
  public function __construct(
    SamlMessage $message,
    SamlProviderInterface $samlProvider
  ) {
    $this->message = $message;
    $this->samlProvider = $samlProvider;
  }

  /**
   * Get the SAML message.
   *
   * @return LightSaml\Model\Protocol\SamlMessage
   *   The SAML message.
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * Set the SAML message.
   *
   * @param LightSaml\Model\Protocol\SamlMessage $message
   *   The SAML message.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setMessage(SamlMessage $message) {
    $this->message = $message;
    return $this;
  }

  /**
   * Get the SAML provider.
   *
   * @return Drupal\saml\Entity\SamlProviderInterface
   *   The SAML provider.
   */
  public function getSamlProvider() {
    return $this->samlProvider;
  }

}
