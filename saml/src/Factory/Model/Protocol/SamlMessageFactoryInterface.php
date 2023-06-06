<?php

namespace Drupal\saml\Factory\Model\Protocol;

use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Entity\ServiceProviderInterface;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides a saml message factory interface.
 */
interface SamlMessageFactoryInterface {

  /**
   * Create a SAML message.
   *
   * @param Drupal\saml\Entity\ServiceProviderInterface $provider
   * @return void
   */
  public function create(SamlProviderInterface $provider);

  /**
   * Create a SAML message from the HTTP request.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   The SAML provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The symfony http request.
   *
   * @return LightSaml\Model\Protocol\SamlMessage|null
   *   The SAML message.
   */
  public function createFromRequest(
    SamlProviderInterface $provider,
    Request $request
  );

}
