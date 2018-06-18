<?php

namespace Drupal\saml\Event;

use Drupal\user\UserInterface;
use LightSaml\Model\Protocol\Response;
use Symfony\Component\EventDispatcher\Event;
use Drupal\saml\Entity\ServiceProviderInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Undocumented class
 */
class SamlResponseAlterEvent extends Event {

  /**
   * The authenticated user.
   *
   * @var Drupal\user\UserInterface
   */
  protected $account;

  /**
   * The saml service provider.
   *
   * @var Drupal\saml\Entity\ServiceProviderInterface
   */
  protected $serviceProvider;

  /**
   * The saml response.
   *
   * @var LightSaml\Model\Protocol\Response
   */
  protected $response;

  /**
   * Constructor for SamlResponseAlterEvent.
   *
   * @param LightSaml\Model\Protocol\Response $response
   *   The saml response.
   * @param Drupal\saml\Entity\ServiceProviderInterface $serviceProvider
   *   The saml service provider.
   * @param Drupal\Core\Session\AccountInterface $account
   *   The authenticated user.
   */
  public function __construct(
    Response $response,
    ServiceProviderInterface $serviceProvider,
    AccountInterface $account
  ) {
    $this->response = $response;
    $this->serviceProvider = $serviceProvider;
    $this->account = $account;
  }

  /**
   * Get the saml response.
   *
   * @return LightSaml\Model\Protocol\Response
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * Set the saml response
   *
   * @param LightSaml\Model\Protocol\Response $response
   *   The saml response.
   *
   * @return self
   *   Returns itself for method chaining.
   */
  public function setResponse(Response $response) {
    $this->response = $response;
    return $this;
  }

  /**
   * Get the authenticated user.
   *
   * @return Drupal\Core\Session\AccountInterface
   *   The authenticated user.
   */
  public function getAccount() {
    return $this->account;
  }

  /**
   * Get the saml service provider.
   *
   * @return Drupal\saml\Entity\ServiceProviderInterface
   *   The saml service provider.
   */
  public function getServiceProvider() {
    return $this->serviceProvider;
  }

}
