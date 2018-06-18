<?php

namespace Drupal\saml\Event;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\Event;
use Drupal\saml\Entity\IdentityProviderInterface;

class RedirectLocationAlterEvent extends Event {

  public const ACTION_CONSUME = 'consume';

  protected $identityProvider;

  protected $location;

  public function __construct(
    IdentityProviderInterface $identityProvider,
    Url $location
  ) {
    $this->identityProvider = $identityProvider;
    $this->location = $location;
  }

  public function getIdentityProvider() {
    return $this->identityProvider;
  }

  public function getLocation() {
    return $this->location;
  }

  public function setLocation(Url $location) {
    $this->location = $location;
    return $this;
  }

}
