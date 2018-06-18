<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use Drupal\saml\Entity\IdentityProviderInterface;
use LightSaml\Error\LightSamlValidationException;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;

/**
 * Provides a signature validator for SAML messages and assertions.
 */
class SignatureValidator implements SignatureValidatorInterface {

  /**
   * Identity Provider.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * Constructor for SignatureValidator.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   Identity provider.
   */
  public function __construct(IdentityProviderInterface $identityProvider) {
    $this->identityProvider = $identityProvider;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSignature(AbstractSignatureReader $signature) {
    $wantsSignedResponse = $this
      ->identityProvider
      ->wantsSignedResponse();

    if (!$wantsSignedResponse) {
      return;
    }

    if (!$signature && $wantsSignedResponse) {
      throw new SamlValidationException('Expected signature but not found');
    }

    $signatureAlgorithm = $this
      ->identityProvider
      ->getSignatureAlgorithm();
    $expectedSignatureAlgorithm = $signature->getAlgorithm();

    print_r([
      $signatureAlgorithm,
      $expectedSignatureAlgorithm
    ]);

    if ($signatureAlgorithm !== $expectedSignatureAlgorithm) {
      throw new SamlValidationException(
        'Signature Algorithm does not match expected value'
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
   * Get the Identity Provider signature public key.
   *
   * @return RobRichards\XMLSecLibs\XMLSecurityKey
   *   XML security public key.
   */
  protected function getSignatureKey() {
    $wantsSignedResponse = $this
      ->identityProvider
      ->wantsSignedResponse();

    if (!$wantsSignedResponse) {
      return NULL;
    }

    $certificate = $this
      ->identityProvider
      ->getSignatureCertificate();

    return KeyHelper::createPublicKey(
      (new X509Certificate())->loadPem($certificate)
    );
  }

}
