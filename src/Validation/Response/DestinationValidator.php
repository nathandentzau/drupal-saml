<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\Context\Profile\MessageContext;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DestinationValidator extends ResponseValidatorBase {

  protected $request;

  public function __construct(RequestStack $requestStack) {
    $this->request = $requestStack->getMasterRequest();
  }

  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $destination = $context->getMessage()->getDestination();

    if ($destination !== $this->request->getUri()) {
      throw new SamlValidationException(
        'Message destination does not match current URI'
      );
    }
  }

}
