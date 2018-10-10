<?php

namespace Drupal\saml\Field;

use LightSaml\Model\Assertion\Attribute;
use Drupal\saml\Exception\SamlValidationException;

/**
 * Provides a trait for optional attribute values.
 */
trait OptionalAttributeValueTrait {

  /**
   * {@inheritdoc}
   */
  public function validateValue(Attribute $attribute): void {
    // Do nothing, validation is optional.
  }

}
