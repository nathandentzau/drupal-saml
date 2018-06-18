<?php

namespace Drupal\saml\Entity;

interface SamlProviderInterface {

  /**
   * Check if the Identity Provider should encrypt responses.
   *
   * @return bool
   *   Whether responses should be encrypted.
   */
  public function wantsEncryptedResponse();

  /**
   * Get the Identity Provider encryption algorithm.
   *
   * @return string
   *   The encryption algorithm.
   *
   * @see RobRichards\XMLSecLibs\XMLSecurityKey
   */
  public function getEncryptionAlgorithm();

  /**
   * Get the encryption certificate.
   *
   * @return string
   *   The encryption certificate.
   */
  public function getEncryptionCertificate();

  /**
   * Check if the Identity Provider should sign responses.
   *
   * @return string
   *   Whether responses should be signed.
   */
  public function wantsSignedResponse();

  /**
   * Get the signature algorithm.
   *
   * @return string
   *   The encryption algorithm.
   *
   * @see RobRichards\XMLSecLibs\XMLSecurityKey
   */
  public function getSignatureAlgorithm();

  /**
   * Get the signature certificate.
   *
   * @return string
   *   The signature certificate.
   */
  public function getSignatureCertificate();

  /**
   * Get the Identity Provider issuer.
   *
   * @return string
   *   The issuer.
   */
  public function getIssuer();

  /**
   * Get the Identity Provider issuer format.
   *
   * @return string
   *   The issuer format.
   *
   * @see LightSaml\SamlConstants
   */
  public function getIssuerFormat();

  /**
   * Get the Name ID format.
   *
   * @return string
   *   The Name ID.
   *
   * @see LightSaml\SamlConstants
   */
  public function getNameIdFormat();

}
