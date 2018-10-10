<?php

namespace Drupal\saml\Field;

use Drupal\saml\Entity\SamlProviderInterface;

/**
 * Provides a field mapper manager.
 */
class FieldMapperManager implements FieldMapperManagerInterface {

  /**
   * Field mappers.
   *
   * @var array
   */
  protected $mappers = [];

  /**
   * {@inheritdoc}
   */
  public function addFieldMapper(FieldMapperInterface $mapper) {
    $this->mappers[get_class($mapper)] = $mapper;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function load($class) {
    if (!is_subclass_of($class, FieldMapperInterface::class, TRUE)) {
      throw new \InvalidArgumentException(
        'Class must be an instance of ' . FieldMapperInterface::class
      );
    }

    if (empty($this->mappers[$class])) {
      throw new \LogicException(
        "The '{$class}' is not registered"
      );
    }

    return $this->mappers[$class];
  }

  /**
   * {@inheritdoc}
   */
  public function loadAll() {
    return $this->mappers;
  }

  /**
   * {@inheritdoc}
   */
  public function loadByProvider(SamlProviderInterface $provider) {
    $mappers = [];

    foreach ($this->mappers as $mapper) {
      if ($mapper->applies($provider)) {
        $mappers[] = $mapper;
      }
    }

    return $mappers;
  }

  /**
   * {@inheritdoc}
   */
  public function remove($class) {
    unset($this->mappers[$class]);
    return $this;
  }

}
