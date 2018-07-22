<?php

namespace Drupal\saml_example\Attribute;

use LightSaml\Model\Assertion\Attribute;
use Drupal\saml\Attribute\AttributeFieldMapBase;
use Drupal\saml\Attribute\RequiredAttributeValueTrait;

class FirstNameAttributeFieldMap extends AttributeFieldMapBase {

  use RequiredAttributeValueTrait;

  public function getAttributeName(): string {
    return 'FirstName';
  }

  public function getFieldName(): string {
    return 'field_user_first_name';
  }

  public function normalizeAttributeValue(Attribute $attribute) {
    $attributeValue = parent::normalizeAttributeValue($attribute);
    return ucfirst($attributeValue);
  }

}
