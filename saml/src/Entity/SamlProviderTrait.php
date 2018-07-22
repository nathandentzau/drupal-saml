<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

trait SamlProviderTrait {

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

}
