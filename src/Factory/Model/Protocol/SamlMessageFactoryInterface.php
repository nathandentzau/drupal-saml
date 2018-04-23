<?php

namespace Drupal\saml\Factory\Model\Protocol;

use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Entity\IdentityProviderInterface;

interface SamlMessageFactoryInterface {

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
