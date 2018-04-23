<?php

namespace Drupal\saml\Factory\Model\Metadata;

use Drupal\saml\Entity\IdentityProviderInterface;

interface EntityDescriptorFactoryInterface {

  /**
   * Create a metadata files for a service provider.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The identity provider entity.
   *
   * @return LightSaml\Model\Metadata\EntityDescriptor
   *   A SAML entity descriptor.
   */
  public function createServiceProvider(
    IdentityProviderInterface $identityProvider
  );

}
