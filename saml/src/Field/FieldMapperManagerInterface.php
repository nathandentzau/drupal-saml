<?php

namespace Drupal\saml\Field;

use Drupal\saml\Field\FieldMapperInterface;
use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides a field mapper manager interface.
 */
interface FieldMapperManagerInterface {

  /**
   * Add a field mapper class to the manager.
   *
   * @param Drupal\saml\Field\FieldMapperInterface $mapper
   *   The field mapper.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function addFieldMapper(FieldMapperInterface $mapper);

  /**
   * Load a field mapper by it's full class namespace.
   *
   * @param string $class
   *   The full class namespace of the field mapper class.
   *
   * @return Drupal\saml\Field\FieldMapperInterface
   *   The field mapper.
   *
   * @throws \InvalidArgumentException
   *   Throws this exception if the class parameter is not an instance of
   *   Drupal\saml\Field\FieldMapperInterface.
   * @throws \LogicException
   *   Throws this exception if the field mapper is not registered.
   */
  public function load($class);

  /**
   * Load all the field mapper classes.
   *
   * @return Drupal\saml\Field\FieldMapperInterface[]
   *   A list of field mapper interfaces indexed by their full class namespace.
   */
  public function loadAll();

  /**
   * Load the field mappers by SAML provider.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   The SAML provider entity.
   *
   * @return Drupal\saml\Field\FieldMapperInterface[]
   *   A list of field mapper interfaces indexed by their full class namespace.
   */
  public function loadByProvider(SamlProviderInterface $provider);

  /**
   * Remove a field mapper class by it's full class namespace.
   *
   * @param string $class
   *   The full class namespace of the field mapper class.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function remove($class);

}
