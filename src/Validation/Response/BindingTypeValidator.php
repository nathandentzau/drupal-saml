<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\SamlConstants;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BindingTypeValidator extends ResponseValidatorBase {

  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $bindingType = $context->getBindingType();

    if ($bindingType !== SamlConstants::BINDING_SAML2_HTTP_POST) {
      throw new SamlValidationException(
        sprintf('Unexpected binding type %s', $bindingType)
      );
    }
  }

}
