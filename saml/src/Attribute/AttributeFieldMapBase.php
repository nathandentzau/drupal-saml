<?php

namespace Drupal\saml\Attribute;

use LightSaml\Model\Assertion\Attribute;

abstract class AttributeFieldMapBase implements AttributeFieldMapInterface {

  public function normalizeAttributeValue(Attribute $attribute) {
    return $attribute->getFirstAttributeValue($this->getAttributeName());
  }

  public function validateAttribute(Attribute $attribute): void {
    // Do nothing, validation is optional.
  }

}
