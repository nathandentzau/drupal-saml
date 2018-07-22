<?php

namespace Drupal\saml\Event;

use Drupal\user\UserInterface;
use LightSaml\Model\Protocol\Response;
use Symfony\Component\EventDispatcher\Event;
use Drupal\saml\Entity\IdentityProviderInterface;

/**
 * Provides a user provision event.
 */
class ProvisionSamlUserEvent extends Event {

  /**
   * Event machine name.
   */
  const NAME = 'saml.provision_user';

  /**
   * Drupal user.
   *
   * @var Drupal\user\UserInterface
   */
  protected $account;

  /**
   * SAML message.
   *
   * @var LightSaml\Model\Protocol\Response
   */
  protected $message;

  /**
   * Identity Provider entity.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * Constructor for ProvisionUserEvent.
   *
   * @param Drupal\user\UserInterface $account
   *   A Drupal user.
   * @param LightSaml\Model\Protocol\Response $message
   *   A SAML message.
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   An Identity Provider.
   */
  public function __construct(
    UserInterface $account,
    Response $message,
    IdentityProviderInterface $identityProvider
  ) {
    $this->account = $account;
    $this->message = $message;
    $this->identityProvider = $identityProvider;
  }

  /**
   * Get the Drupal user entity.
   *
   * @return Drupal\user\UserInterface
   *   A Drupal user.
   */
  public function getAccount() {
    return $this->account;
  }

  /**
   * Get the SAML message.
   *
   * @return LightSaml\Model\Protocol\Response
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * Get the Identity Provider entity.
   *
   * @return Drupal\saml\Entity\IdentityProviderInterface
   *   An Identity Provider entity.
   */
  public function getIdentityProvider() {
    return $this->identityProvider;
  }

}
