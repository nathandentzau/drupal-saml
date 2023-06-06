<?php

namespace Drupal\saml\Event;

use Drupal\user\UserInterface;
use LightSaml\Model\Protocol\Response;
use Drupal\Component\EventDispatcher\Event;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides a user provision event.
 */
class ProvisionUserEvent extends Event {

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
   * Service Provider entity.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $serviceProvider;

  /**
   * Constructor for ProvisionUserEvent.
   *
   * @param Drupal\user\UserInterface $account
   *   A Drupal user.
   * @param LightSaml\Model\Protocol\Response $message
   *   A SAML message.
   * @param Drupal\saml\Entity\SamlProviderInterface $serviceProvider
   *   An Service Provider.
   */
  public function __construct(
    UserInterface $account,
    Response $message,
    SamlProviderInterface $serviceProvider
  ) {
    $this->account = $account;
    $this->message = $message;
    $this->serviceProvider = $serviceProvider;
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
   * Get the Service Provider entity.
   *
   * @return Drupal\saml\Entity\SamlProviderInterface
   *   An Service Provider entity.
   */
  public function getServiceProvider() {
    return $this->serviceProvider;
  }

}
