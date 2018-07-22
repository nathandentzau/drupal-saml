<?php

namespace Drupal\saml_example\User;

use Drupal\saml\User\SamlUserProvisionerBase;
use Drupal\saml_example\Attribute\FirstNameAttributeFieldMap;

class ExampleUserProvisioner extends SamlUserProvisionerBase {

  protected function getAttributeFieldMapClasses(): array {
    return [FirstNameAttributeFieldMap::class];
  }

  protected function getIdentityProviderIdentifier(): string {
    return 'example';
  }

}
