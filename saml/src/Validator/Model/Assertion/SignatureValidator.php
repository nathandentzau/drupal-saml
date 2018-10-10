<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Error\LightSamlValidationException;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;

/**
 * Provides a signature validator for SAML messages and assertions.
 */
class SignatureValidator implements SignatureValidatorInterface {

  /**
   * Service Provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $provider;

  /**
   * Constructor for SignatureValidator.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   Service Provider.
   */
  public function __construct(SamlProviderInterface $provider) {
    $this->provider = $provider;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSignature(AbstractSignatureReader $signature) {
    $wantsSignedResponse = $this->provider->getSignedResponse();

    if (!$wantsSignedResponse) {
      return;
    }

    if (!$signature && $wantsSignedResponse) {
      throw new SamlValidationException('Expected signature but not found');
    }

    $signatureAlgorithm = $signature->getAlgorithm();
    $expectedSignatureAlgorithm = $this->provider
      ->getSignatureResponseAlgorithm();

    if ($signatureAlgorithm !== $expectedSignatureAlgorithm) {
      throw new SamlValidationException(
        sprintf(
          'Signature Algorithm does not match expected value. Expected %s, received %s',
          $expectedSignatureAlgorithm,
          $signatureAlgorithm
        )
      );
    }

    try {
      $signature->validate($this->getSignatureKey());
    }
    catch (LightSamlSecurityException $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

  /**
   * Get the Service Provider signature public key.
   *
   * @return RobRichards\XMLSecLibs\XMLSecurityKey
   *   XML security public key.
   */
  protected function getSignatureKey() {
    $wantsSignedResponse = $this->provider->getSignedResponse();

    if (!$wantsSignedResponse) {
      return NULL;
    }

    $certificate = $this
      ->provider
      ->getSignatureResponseCertificate();

    return KeyHelper::createPublicKey(
      (new X509Certificate())->loadPem($certificate)
    );
  }

}
