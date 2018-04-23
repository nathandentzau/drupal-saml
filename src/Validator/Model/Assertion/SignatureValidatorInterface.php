<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Model\XmlDSig\AbstractSignatureReader;

interface SignatureValidatorInterface {

  /**
   * Validate SAML or assertion signature.
   *
   * @param LightSaml\Model\XmlDSig\AbstractSignatureReader $signature
   *   SAML signature.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  public function validateSignature(AbstractSignatureReader $signature);

}
