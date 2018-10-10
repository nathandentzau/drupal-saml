<?php

namespace Drupal\saml\Field;

use LightSaml\Model\Assertion\Attribute;
use Drupal\saml\Exception\SamlValidationException;

/**
 * Provides a required attribute value trait.
 */
trait RequiredAttributeValueTrait {

  /**
   * {@inheritdoc}
   */
  public function validateValue(Attribute $attribute): void {
    $attributeValue = $attribute->getFirstAttributeValue(
      $this->getAttributeName()
    );

    if (mb_strlen($attributeValue) === 0) {
      throw new SamlValidationException(
        sprintf('Attribute %s is required', $this->getAttributeName())
      );
    }
  }

}
