<?php

namespace Drupal\saml\Event;

use Symfony\Component\EventDispatcher\Event;
use LightSaml\Model\Metadata\EntityDescriptor;

/**
 * Provides an entity descriptor alter event.
 */
class EntityDescriptorAlterEvent extends Event {

  /**
   * Event name.
   */
  const NAME = 'saml.entity_descriptor_alter';

  /**
   * Entity descriptor.
   *
   * @var LightSaml\Model\Metadata\EntityDescriptor
   */
  protected $entityDescriptor;

  /**
   * Constructor for EntityDescriptorAlterEvent.
   *
   * @param EntityDescriptor $entityDescriptor
   *   The entity descriptor.
   */
  public function __construct(EntityDescriptor $entityDescriptor) {
    $this->entityDescriptor = $entityDescriptor;
  }

  /**
   * Get the entity descriptor.
   *
   * @return LightSaml\Model\Metadata\EntityDescriptor
   *   The entity descriptor.
   */
  public function getEntityDescriptor() {
    return $this->entityDescriptor;
  }

  /**
   * Set the entity descriptor.
   *
   * @param LightSaml\Model\Metadata\EntityDescriptor $entityDescriptor
   *   The entity descriptor.
   *
   * @return self
   *   Returns itself for a fluid interface.
   */
  public function setEntityDescriptor(EntityDescriptor $entityDescriptor) {
    $this->entityDescriptor = $entityDescriptor;
    return $this;
  }

}
