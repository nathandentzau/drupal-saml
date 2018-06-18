<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Url;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Provides an Identity Provider configuration entity.
 *
 * @ConfigEntityType(
 *   id = "identity_provider",
 *   label = @Translation("Identity Provider"),
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   }
 * )
 */
class IdentityProvider extends ConfigEntityBase implements IdentityProviderInterface {

  use SamlProviderTrait;

  /**
   * Identity provider issuer.
   *
   * @var string
   */
  protected $issuer;

  /**
   * Response encryption key.
   *
   * This can be either a path on the file system or the contents of the
   * certificate.
   *
   * @var string
   */
  protected $encryption_key;

  /**
   * Mail attribute name.
   *
   * A SAML attribute
   *
   * @var string
   */
  protected $mail_attribute;

  /**
   * {@inheritdoc}
   */
  public function getEncryptionKey() {
    $key = $this->encryption_key;

    if (file_exists($key)) {
      return file_get_contents($key);
    }

    return $key;
  }

  /**
   * {@inheritdoc}
   */
  public function getIssuer() {
    return $this->issuer;
  }

  /**
   * {@inheritdoc}
   */
  public function getMailAttribute() {
    return $this->mail_attribute;
  }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function getAssertionConsumerServiceUrl() {
  //   return Url::fromRoute(
  //     'saml.sp.consume',
  //     ['identityProvider' => $this->id()],
  //     ['absolute' => TRUE]
  //   )->toString();
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function getMetadataUrl() {
  //   return Url::fromRoute(
  //     'saml.sp.metadata',
  //     ['identityProvider' => $this->id()],
  //     ['absolute' => TRUE]
  //   )->toString();
  // }

}
