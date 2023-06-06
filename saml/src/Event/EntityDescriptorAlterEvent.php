<?php

namespace Drupal\saml\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Model\Metadata\EntityDescriptor;

/**
 * Provides an entity descriptor (metadata) alter event.
 */
class EntityDescriptorAlterEvent extends Event {

  /**
   * The entity descriptor.
   *
   * @var LightSaml\Model\Metadata\EntityDescriptor
   */
  protected $entityDescriptor;

  /**
   * The SAML provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $samlProvider;

  /**
   * Constructor for EntityDescriptorAlterEvent.
   *
   * @param LightSaml\Model\Metadata\EntityDescriptor $entityDescriptor
   *   The entity descriptor.
   * @param Drupal\saml\Entity\SamlProviderInterface $samlProvider
   *   The SAML provider.
   */
  public function __construct(
    EntityDescriptor $entityDescriptor,
    SamlProviderInterface $samlProvider
  ) {
    $this->entityDescriptor = $entityDescriptor;
    $this->samlProvider = $samlProvider;
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

  /**
   * Get the SAML provider.
   *
   * @return Drupal\saml\Entity\SamlProviderInterface
   *   The SAML provider.
   */
  public function getSamlProvider() {
    return $this->samlProvider;
  }

}
