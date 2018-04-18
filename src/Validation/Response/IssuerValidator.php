<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Validator\Model\NameId\NameIdValidator;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IssuerValidator extends ResponseValidatorBase {

  protected $validator;

  public function __construct(NameIdValidatorInterface $validator = NULL) {
    $this->validator = $validator ?: new NameIdValidator();
  }

  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $issuer = $context->getMessage()->getIssuer();

    if (!$issuer) {
      throw new SamlValidationException('Message must contain an Issuer');
    }

    if ($issuer->getFormat() !== $identityProvider->getIssuerFormat()) {
      throw new SamlValidationException(
        sprintf(
          'Invalid format for Issuer. Expected %s, Received %s',
          $identityProvider->getIssuerFormat(),
          $issuer->getFormat()
        )
      );
    }

    if ($issuer->getValue() !== $identityProvider->getIssuer()) {
      throw new SamlValidationException('Invalid issuer');
    }

    try {
      $this->validator->validateNameId($issuer);
    }
    catch (\Exception $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

}
