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
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "encrypted_response",
 *     "encryption_response_algorithm",
 *     "encryption_response_certificate",
 *     "encryption_response_key",
 *     "signed_response",
 *     "signature_response_algorithm",
 *     "signature_response_certificate",
 *     "signature_response_key",
 *     "signed_request",
 *     "signature_request_algorithm",
 *     "signature_request_certificate",
 *     "signature_request_key",
 *     "issuer_format",
 *     "issuer",
 *     "name_id_format",
 *     "email_attribute",
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
