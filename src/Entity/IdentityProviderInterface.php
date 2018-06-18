<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface IdentityProviderInterface extends SamlProviderInterface, ConfigEntityInterface {

  /**
   * Get the encryption key.
   *
   * @return string
   *   The encryption key.
   */
  public function getEncryptionKey();

  /**
   * Get the mail attribute name.
   *
   * @return string
   *   The name of the mail attribute.
   */
  public function getMailAttribute();

  /**
   * Get the Identity Provider Entity ID.
   *
   * @return string
   *   The full URI of an Entity ID.
   */
  public function getIssuer();

}
