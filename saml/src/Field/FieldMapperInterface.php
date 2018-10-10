<?php

namespace Drupal\saml\Field;

use LightSaml\Model\Assertion\Attribute;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides a field mapper interface.
 */
interface FieldMapperInterface {

  /**
   * Check if this field applies to the SAML provider.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   The SAML provider entity.
   *
   * @return bool
   *   Whether this field should be applied to the user for this SAML provider.
   */
  public function applies(SamlProviderInterface $provider);

  /**
   * Get the SAML attribute name.
   *
   * @return string
   *   The SAML attribute name.
   */
  public function getAttributeName();

  /**
   * Get the User entity field name.
   *
   * @return string
   *   The machine name of the field to map the attribute value to.
   */
  public function getFieldName();

  /**
   * Builds an attribute field value to be saved on the user entity.
   *
   * @param LightSaml\Model\Assertion\Attribute $attribute
   *   A SAML message attribute.
   *
   * @return mixed
   *   The user entity field value. This can be a number of return types
   *   depending on the field type.
   */
  public function buildValue(Attribute $attribute);

  /**
   * Validate the SAML attribute value.
   *
   * @param LightSaml\Model\Assertion\Attribute $attribute
   *   A SAML message attribute.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   *   This exception is thrown if the SAML attribute value fails validation.
   */
  public function validateValue(Attribute $attribute);

}
