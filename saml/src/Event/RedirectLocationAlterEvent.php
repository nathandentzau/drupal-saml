<?php

namespace Drupal\saml\Event;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\Event;
use Drupal\saml\Entity\IdentityProviderInterface;

/**
 * Provides an event to later the redirect location in the inbound flow.
 */
class RedirectLocationAlterEvent extends Event {

  /**
   * The Identity Provider.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * The redirect location.
   *
   * @var Drupal\Core\Url
   */
  protected $location;

  /**
   * Constructor for RedirectLocationAlterEvent.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The Identity Provider.
   * @param Drupal\Core\Url $location
   *   The redirect location.
   */
  public function __construct(
    IdentityProviderInterface $identityProvider,
    Url $location
  ) {
    $this->identityProvider = $identityProvider;
    $this->location = $location;
  }

  /**
   * Get the Identity Provider.
   *
   * @return Drupal\saml\Entity\IdentityProviderInterface
   *   The Identity Provider.
   */
  public function getIdentityProvider() {
    return $this->identityProvider;
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
