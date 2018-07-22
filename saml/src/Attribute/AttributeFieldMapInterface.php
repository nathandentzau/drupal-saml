<?php

namespace Drupal\saml\Attribute;

use LightSaml\Model\Assertion\Attribute;

interface AttributeFieldMapInterface {

  public function getAttributeName(): string;

  public function getFieldName(): string;

  public function normalizeAttributeValue(Attribute $attribute);

  public function validateAttribute(Attribute $attribute): void;

}
