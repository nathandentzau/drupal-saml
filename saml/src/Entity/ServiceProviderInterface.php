<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface ServiceProviderInterface extends SamlProviderInterface, ConfigEntityInterface {

  /**
   * Get the Audience Restriction.
   *
   * @return string
   *   The AuthnContext AudienceRestriction element value.
   */
  public function getAudienceRestriction();

  /**
   * Get the signature key.
   *
   * @return string
   *   The signature key.
   */
  public function getSignatureKey();

}
