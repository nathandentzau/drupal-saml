<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Model\Assertion\AbstractNameID;
use Drupal\saml\Exception\SamlValidationException;

/**
 * Provides an issuer validator.
 */
class CompositeIssuerValidator extends AbstractCompositeNameIdValidator {

  /**
   * {@inheritdoc}
   */
  public function validateNameId(AbstractNameID $nameId): void {
    try {
      $this
        ->getValidator()
        ->validateNameId($nameId);
    }
    catch (LightSamlValidationException $e) {
      throw new SamlValidationException($e->getMessage());
    }

    $issuerFormat = $nameId->getFormat();
    $expectedIssuerFormat = $this
      ->getIdentityProvider()
      ->getIssuerFormat();

    if ($issuerFormat !== $expectedIssuerFormat) {
      throw new SamlValidationException(
        sprintf('Issuer Format %s did not match expected value', $issuerFormat)
      );
    }

    $issuer = $nameId->getValue();
    $expectedIssuer = $this
      ->getIdentityProvider()
      ->getIssuer();

    if ($issuer !== $expectedIssuer) {
      throw new SamlValidationException(
        sprintf('Issuer value %s did not match expected value', $issuer)
      );
    }
  }

}
