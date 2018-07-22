<?php

namespace Drupal\saml\Validator\Model\Protocol;

use LightSaml\Context\Profile\MessageContext;

/**
 * Provides a SAML message validator interface.
 */
interface SamlMessageValidatorInterface {

  /**
   * Validate the SAML message.
   *
   * @param LightSaml\Context\Profile\MessageContext $message
   *   The SAML message context.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  public function validate(MessageContext $message);

}
