<?php

namespace Drupal\saml\Field;

use LightSaml\Model\Assertion\Attribute;

/**
 * Provides a trait to get the first saml attribute value.
 */
trait FirstAttributeValueTrait {

  /**
   * {@inheritdoc}
   */
  public function buildValue(Attribute $attribute) {
    return $attribute->getFirstAttributeValue($this->getAttributeName());
  }

}
