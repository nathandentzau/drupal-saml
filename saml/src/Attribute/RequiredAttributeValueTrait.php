<?php

namespace Drupal\saml\Attribute;

use LightSaml\Model\Assertion\Attribute;
use Drupal\saml\Exception\SamlValidationException;

trait RequiredAttributeValueTrait {

  public function validateAttribute(Attribute $attribute): void {
    $attributeValue = $this->normalizeAttributeValue($attribute);

    if (mb_strlen($attributeValue) === 0) {
      throw new SamlValidationException(
        sprintf('Attribute %s is required', $this->getAttributeName())
      );
    }
  }

}
