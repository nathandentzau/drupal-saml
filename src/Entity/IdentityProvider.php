<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Url;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
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

  /**
   * Identity provider identifier.
   *
   * @var string
   */
  protected $id;

  /**
   * Identity provider label.
   *
   * @var string
   */
  protected $label;

  /**
   * Flag to encrypt responses.
   *
   * @var bool
   */
  protected $encrypted_response;

  /**
   * Response encryption algorithm.
   *
   * @var string
   */
  protected $encryption_algorithm;

  /**
   * Response encryption certificate.
   *
   * This can be either a path on the file system or the contents of the
   * certificate.
   *
   * @var string
   */
  protected $encryption_certificate;

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
   * Flag to sign responses.
   *
   * @var bool
   */
  protected $signed_response;

  /**
   * Response signature certificate.
   *
   * This can be either a path on the file system or the contents of the
   * certificate.
   *
   * @var string
   */
  protected $signature_certificate;

  /**
   * Identity provider issuer.
   *
   * @var string
   */
  protected $issuer;

  /**
   * Identity provider issuer format.
   *
   * @var string
   */
  protected $issuer_format;

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
  public function wantsEncryptedResponse() {
    return (bool) $this->encrypted_response;
  }

  /**
   * {@inheritdoc}
   */
  public function getEncryptionAlgorithm() {
    return $this->encryption_algorithm;
  }

  /**
   * {@inheritdoc}
   */
  public function getEncryptionCertificate() {
    $certificate = $this->encryption_certificate;

    if (file_exists($certificate)) {
      return file_get_contents($certificate);
    }

    return $certificate;
  }

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
  public function wantsSignedResponse() {
    return (bool) $this->signed_response;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureCertificate() {
    $certificate = $this->signature_certificate;

    if (file_exists($certificate)) {
      return file_get_contents($certificate);
    }

    return $certificate;
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
  public function getIssuerFormat() {
    return $this->issuer_format;
  }

  /**
   * {@inheritdoc}
   */
  public function getMailAttribute() {
    return $this->mail_attribute;
  }

}
