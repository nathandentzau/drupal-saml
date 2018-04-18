<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\SamlConstants;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Event\ReceiveSamlResponseEvent;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StatusValidator extends ResponseValidatorBase {

  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $status = $context->getMessage()->getStatus();

    if (!$status) {
      throw new SamlValidationException('No status set in message');
    }

    if (!$status->isSuccess()) {
      throw new SamlValidationException(
        sprintf(
          'Status in message is not Success. Received: %s',
          $status->getStatusCode()->getValue()
        )
      );
    }
  }

}
