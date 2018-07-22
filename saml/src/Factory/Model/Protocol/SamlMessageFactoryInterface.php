<?php

namespace Drupal\saml\Factory\Model\Protocol;

use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Entity\ServiceProviderInterface;
use Drupal\saml\Entity\IdentityProviderInterface;

interface SamlMessageFactoryInterface {

  /**
   * Create a SAML message.
   *
   * @param Drupal\saml\Entity\ServiceProviderInterface $serviceProvider
   *   The Service Provider entity.
   * @param Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \DateTime $currentTime
   *   Optional. The current time.
   *
   * @return LightSaml\Model\Protocol\SamlMessage|null
   *   The SAML message.
   */
  public function create(
    ServiceProviderInterface $serviceProvider,
    AccountInterface $account,
    \DateTime $currentTime = NULL
  );

  /**
   * Create a SAML message from the HTTP request.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The Identity Provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The symfony http request.
   *
   * @return LightSaml\Model\Protocol\SamlMessage|null
   *   The SAML message.
   */
  public function createFromRequest(
    IdentityProviderInterface $identityProvider,
    Request $request
  );

}
