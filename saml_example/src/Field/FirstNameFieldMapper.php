<?php

namespace Drupal\saml_example\Field;

use Drupal\saml\Field\FieldMapperInterface;
use Drupal\saml\Entity\SamlProviderInterface;
use Drupal\saml\Field\FirstAttributeValueTrait;
use Drupal\saml\Field\RequiredAttributeValueTrait;

class FirstNameFieldMapper implements FieldMapperInterface {

  use FirstAttributeValueTrait;
  use RequiredAttributeValueTrait;

  public function applies(SamlProviderInterface $provider) {
    return $provider->id() === 'example';
  }

  public function getAttributeName() {
    return 'FirstName';
  }

  public function getFieldName() {
    return 'field_first_name';
  }

}
