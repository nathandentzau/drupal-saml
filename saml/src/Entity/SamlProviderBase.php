<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Provides a base saml provider entity.
 */
abstract class SamlProviderBase extends ConfigEntityBase implements SamlProviderInterface {

  /**
   * If the response should be encrypted.
   *
   * @var bool
   */
  protected $encrypted_response;

  /**
   * The encryption algorithm.
   *
   * @var string
   */
  protected $encryption_response_algorithm;

  /**
   * The encryption certificate.
   *
   * @var string
   */
  protected $encryption_response_certificate;

  /**
   * The encryption key.
   *
   * @var string
   */
  protected $encryption_response_key;

  /**
   * If the response should be signed.
   *
   * @var bool
   */
  protected $signed_response;

  /**
   * The signature response algorithm.
   *
   * @var string
   */
  protected $signature_response_algorithm;

  /**
   * The signature response certificate.
   *
   * @var string
   */
  protected $signature_response_certification;

  /**
   * The signature response key.
   *
   * @var string
   */
  protected $signature_response_key;

  /**
   * If the request should be signed.
   *
   * @var bool
   */
  protected $signed_request;

  /**
   * The signature request algorithm.
   *
   * @var string
   */
  protected $signature_request_algorithm;

  /**
   * The signature request certificate.
   *
   * @var string
   */
  protected $signature_request_certification;

  /**
   * The signature request key.
   *
   * @var string
   */
  protected $signature_request_key;

  /**
   * The issuer format.
   *
   * @var string
   */
  protected $issuer_format;

  /**
   * The issuer.
   *
   * @var string
   */
  protected $issuer;

  /**
   * The name ID format.
   *
   * @var string
   */
  protected $name_id_format;

  /**
   * The email attribute.
   *
   * @var string
   */
  protected $email_attribute;

  protected $sso_url;

  /**
   * {@inheritdoc}
   */
  public function getEncryptedResponse() {
    return $this->get('encrypted_response');
  }

  /**
   * {@inheritdoc}
   */
  public function setEncryptedResponse($encrypt = TRUE) {
    $this->set('encrypted_response', $encrypt);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEncryptionResponseAlgorithm() {
    return $this->get('encryption_response_algorithm');
  }

  /**
   * {@inheritdoc}
   */
  public function setEncryptionResponseAlgorithm($algorithm) {
    $this->set('encryption_response_algorithm', $algorithm);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEncryptionResponseCertificate() {
    return $this->get('encryption_response_certificate');
  }

  /**
   * {@inheritdoc}
   */
  public function setEncryptionResponseCertificate($certificate) {
    $this->set('encryption_response_certificate', $certificate);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEncryptionResponseKey() {
    return $this->get('encryption_response_key');
  }

  /**
   * {@inheritdoc}
   */
  public function setEncryptionResponseKey($key) {
    $this->set('encryption_response_key', $key);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignedResponse() {
    return $this->get('signed_response');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignedResponse($sign = TRUE) {
    $this->set('signed_response', $sign);
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureResponseAlgorithm() {
    return $this->get('signature_response_algorithm');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureResponseAlgorithm($algorithm) {
    $this->set('signature_response_algorithm', $algorithm);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureResponseCertificate() {
    return $this->get('signature_response_certificate');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureResponseCertificate($certificate) {
    $this->set('signature_response_certificate', $certificate);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureResponseKey() {
    return $this->get('signature_response_key');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureResponseKey($key) {
    $this->set('signature_response_key', $key);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignedRequest() {
    return $this->get('signed_request');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignedRequest($sign = TRUE) {
    $this->set('signed_request', $sign);
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureRequestAlgorithm() {
    return $this->get('signature_request_algorithm');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureRequestAlgorithm($algorithm) {
    $this->set('signature_request_algorithm', $algorithm);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureRequestCertificate() {
    return $this->get('signature_request_certificate');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureRequestCertificate($certificate) {
    $this->set('signature_request_certificate', $certificate);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureRequestKey() {
    return $this->get('signature_request_key');
  }

  /**
   * {@inheritdoc}
   */
  public function setSignatureRequestKey($key) {
    $this->set('signature_request_key', $key);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIssuerFormat() {
    return $this->get('issuer_format');
  }

  /**
   * {@inheritdoc}
   */
  public function setIssuerFormat($format) {
    $this->set('issuer_format', $format);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIssuer() {
    return $this->get('issuer');
  }

  /**
   * {@inheritdoc}
   */
  public function setIssuer($issuer) {
    $this->set('issuer', $issuer);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNameIdFormat() {
    return $this->get('name_id_format');
  }

  /**
   * {@inheritdoc}
   */
  public function setNameIdFormat($format) {
    $this->set('name_id_format', $format);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmailAttribute() {
    return $this->get('email_attribute');
  }

  /**
   * {@inheritdoc}
   */
  public function setEmailAttribute($email) {
    $this->set('email_attribute', $email);
    return $this;
  }

  public function getSingleSignOnUrl() {
    return $this->sso_url;
  }

  public function setSingleSignOnUrl($ssoUrl) {
    $this->sso_url = $ssoUrl;
    return $this;
  }

}
