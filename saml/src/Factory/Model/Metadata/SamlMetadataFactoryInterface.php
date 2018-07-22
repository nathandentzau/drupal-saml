<?php

namespace Drupal\saml\Factory\Model\Metadata;

use Drupal\saml\Entity\SamlProviderInterface;

interface SamlMetadataFactoryInterface {

  /**
   * Create a metadata files for a service provider.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   A SAML provider entity.
   *
   * @return LightSaml\Model\Metadata\EntityDescriptor
   *   A SAML entity descriptor.
   */
  public function create(SamlProviderInterface $provider);

}
