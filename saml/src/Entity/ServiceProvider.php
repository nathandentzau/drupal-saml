<?php

namespace Drupal\saml\Entity;

/**
 * Provides an Service Provider configuration entity.
 *
 * @ConfigEntityType(
 *   id = "service_provider",
 *   label = @Translation("Service Provider"),
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   }
 * )
 */
class ServiceProvider extends SamlProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getSignatureResponseKey() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureResponseKey($key) {
    return $this;
  }

}
