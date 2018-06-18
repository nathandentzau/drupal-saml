<?php

namespace Drupal\saml\Validator\Model\Protocol;

use LightSaml\Context\Profile\MessageContext;

interface SamlMessageValidatorInterface {

  public function validate(MessageContext $message);

}
