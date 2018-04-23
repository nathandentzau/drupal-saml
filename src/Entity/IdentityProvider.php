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
   * Response signature algorithm.
   *
   * @var string
   */
  protected $signature_algorithm;

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
   * Response Name ID format.
   *
   * @var string
   */
  protected $name_id_format;

  /**
   * SAML AuthnContext.
   *
   * @var string
   */
  protected $authn_context;

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
  public function getSignatureAlgorithm() {
    return $this->signature_algorithm;
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
  public function getNameIdFormat() {
    return $this->name_id_format;
  }

  /**
   * {@inheritdoc}
   */
  public function getMailAttribute() {
    return $this->mail_attribute;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthnContext() {
    return $this->authn_context;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityId() {
    return Url::fromRoute(
      'saml.sp',
      ['identityProvider' => $this->id()],
      ['absolute' => TRUE]
    )->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function getAssertionConsumerServiceUrl() {
    return Url::fromRoute(
      'saml.sp.consume',
      ['identityProvider' => $this->id()],
      ['absolute' => TRUE]
    )->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataUrl() {
    return Url::fromRoute(
      'saml.sp.metadata',
      ['identityProvider' => $this->id()],
      ['absolute' => TRUE]
    )->toString();
  }

}
