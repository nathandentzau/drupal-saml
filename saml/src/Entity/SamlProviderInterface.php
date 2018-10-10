<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a SAML provider interface.
 */
interface SamlProviderInterface extends EntityInterface {

  /**
   * Get the value if the response should be encrypted.
   *
   * @return bool
   *   If the response should be encrypted.
   */
  public function getEncryptedResponse();

  /**
   * Set the value if the response should be encrypted.
   *
   * @param bool $encrypt
   *   Encrypt the response.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setEncryptedResponse($encrypt = TRUE);

  /**
   * Get the encryption algorithm.
   *
   * @return string
   *   The encryption algorithm.
   */
  public function getEncryptionResponseAlgorithm();

  /**
   * Set the encryption algorithm.
   *
   * @param string $algorithm
   *   The encryption algorithm.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setEncryptionResponseAlgorithm($algorithm);

  /**
   * Get the encryption certificate.
   *
   * @return string
   *   The encryption certificate.
   */
  public function getEncryptionResponseCertificate();

  /**
   * Set the encryption certificate.
   *
   * @param string $certificate
   *   The encryption certificate.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setEncryptionResponseCertificate($certificate);

  /**
   * Get the encryption key.
   *
   * @return string
   *   The encryption key.
   */
  public function getEncryptionResponseKey();

  /**
   * Set the encryption key.
   *
   * @param string $key
   *   The encryption key.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setEncryptionResponseKey($key);

  /**
   * Get the value if the response should be signed.
   *
   * @return bool
   *   If the response should be signed.
   */
  public function getSignedResponse();

  /**
   * Set the value if the response should be signed.
   *
   * @param bool $sign
   *   If the response should be signed
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignedResponse($sign = TRUE);

  /**
   * Get the signature algorithm.
   *
   * @return string
   *   The signature algorithm.
   */
  public function getSignatureResponseAlgorithm();

  /**
   * Set the signature algorithm.
   *
   * @param string $algorithm
   *   The signature algorithm.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignatureResponseAlgorithm($algorithm);

  /**
   * Get the signature certificate.
   *
   * @return string
   *   The signature certificate.
   */
  public function getSignatureResponseCertificate();

  /**
   * Set the signature certificate.
   *
   * @param string $certificate
   *   The signature certificate.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignatureResponseCertificate($certificate);

  /**
   * Get the signature key.
   *
   * @return string
   *   The signature key.
   */
  public function getSignatureResponseKey();

  /**
   * Set the signature key.
   *
   * @param string $key
   *   The signature key.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignatureResponseKey($key);

  /**
   * Get the value if the response should be signed.
   *
   * @return bool
   *   If the response should be signed.
   */
  public function getSignedRequest();

  /**
   * Set the value if the request should be signed.
   *
   * @param bool $sign
   *   If the request should be signed
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignedRequest($sign = TRUE);

  /**
   * Get the signature algorithm.
   *
   * @return string
   *   The signature algorithm.
   */
  public function getSignatureRequestAlgorithm();

  /**
   * Set the signature algorithm.
   *
   * @param string $algorithm
   *   The signature algorithm.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignatureRequestAlgorithm($algorithm);

  /**
   * Get the signature certificate.
   *
   * @return string
   *   The signature certificate.
   */
  public function getSignatureRequestCertificate();

  /**
   * Set the signature certificate.
   *
   * @param string $certificate
   *   The signature certificate.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignatureRequestCertificate($certificate);

  /**
   * Get the signature key.
   *
   * @return string
   *   The signature key.
   */
  public function getSignatureRequestKey();

  /**
   * Set the signature key.
   *
   * @param string $key
   *   The signature key.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSignatureRequestKey($key);

  /**
   * Get the issuer format.
   *
   * @return string
   *   The issuer format.
   */
  public function getIssuerFormat();

  /**
   * Set the issuer format.
   *
   * @param string $format
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setIssuerFormat($format);

  /**
   * Get the issuer.
   *
   * @return string
   *   The issuer.
   */
  public function getIssuer();

  /**
   * Set the issuer.
   *
   * @param string $issuer
   *   The issuer.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setIssuer($issuer);

  /**
   * Get the name ID format.
   *
   * @return string
   *   The name ID format.
   */
  public function getNameIdFormat();

  /**
   * Set the name ID format.
   *
   * @param string $format
   *   The name ID format.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setNameIdFormat($format);

  /**
   * Get the email attribute.
   *
   * This attribute is used to create the user account.
   *
   * @return string
   *   The email attribute.
   */
  public function getEmailAttribute();

  /**
   * Set the email attribute.
   *
   * This attribute is used to create the user account.
   *
   * @param string $email
   *   The email attribute.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setEmailAttribute($email);

  /**
   * Get the single sign on URL.
   *
   * @return string
   *   The single sign on URL.
   */
  public function getSingleSignOnUrl();

  /**
   * Set the single sign on URL.
   *
   * @param string $ssoUrl
   *   The single sign on URL.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setSingleSignOnUrl($ssoUrl);

}
