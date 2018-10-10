<?php

namespace Drupal\saml\Event;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\Event;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides an event to later the redirect location in the inbound flow.
 */
class RedirectLocationAlterEvent extends Event {

  /**
   * The Service Provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $serviceProvider;

  /**
   * The redirect location.
   *
   * @var Drupal\Core\Url
   */
  protected $location;

  /**
   * Constructor for RedirectLocationAlterEvent.
   *
   * @param Drupal\saml\Entity\SamlProviderInterfacee $serviceProvider
   *   The Service Provider.
   * @param Drupal\Core\Url $location
   *   The redirect location.
   */
  public function __construct(
    SamlProviderInterface $serviceProvider,
    Url $location
  ) {
    $this->serviceProvider = $serviceProvider;
    $this->location = $location;
  }

  /**
   * Get the Service Provider.
   *
   * @return Drupal\saml\Entity\SamlProviderInterface
   *   The Service Provider.
   */
  public function getServiceProvider() {
    return $this->serviceProvider;
  }

  /**
   * Get the redirect location.
   *
   * @return Drupal\Core\Url
   *   The redirect location.
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * Set the redirect location.
   *
   * @param Drupal\Core\Url $location
   *   The redirect location.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setLocation(Url $location) {
    $this->location = $location;
    return $this;
  }

}
