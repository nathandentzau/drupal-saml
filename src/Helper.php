<?php

namespace Drupal\saml;

use LightSaml\Credential\KeyHelper;
use LightSaml\Model\Protocol\Response;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\X509Certificate;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use LightSaml\Model\Context\DeserializationContext;

/**
 * Provides a helper class for the module.
 */
final class Helper {

  /**
   * Decrypt SAML assertions.
   *
   * @param LightSaml\Model\Protocol\Response $message
   *   SAML Response message.
   * @param string $certificate
   *   X509 certificate.
   * @param string $key
   *   RSA private key.
   * @param string $encryptionAlgorithm
   *   XML encryption algorithm.
   */
  public static function decryptAssertions(
    Response $message,
    $certificate,
    $key,
    $encryptionAlgorithm = XMLSecurityKey::RSA_SHA256
  ) {
    $certificate = (new X509Certificate())->loadPem($certificate);
    $key = KeyHelper::createPrivateKey($key, NULL, FALSE, $encryptionAlgorithm);
    $credential = new X509Credential($certificate, $key);

    foreach ($message->getAllEncryptedAssertions() as $encryptedAssertion) {
      $assertion = $encryptedAssertion->decryptMultiAssertion(
        [$credential],
        new DeserializationContext()
      );

      $message->addAssertion($assertion);
    }
  }

}
