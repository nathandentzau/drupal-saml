<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\SamlConstants;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Validator\Model\NameId\NameIdValidator;
use LightSaml\Validator\Model\Subject\SubjectValidator;
use LightSaml\Validator\Model\Statement\StatementValidator;
use LightSaml\Validator\Model\Assertion\AssertionValidatorInterface;
use LightSaml\Validator\Model\Assertion\AssertionValidator as Validator;

/**
 * Provides an assertion validator.
 */
class AssertionValidator extends ResponseValidatorBase {

  /**
   * LightSaml assertion validator.
   *
   * @var LightSaml\Validator\Model\Assertion\AssertionValidatorInterface
   */
  protected $validator;

  /**
   * Constructor for AssertionValidator.
   *
   * @param LightSaml\Validator\Model\Assertion\AssertionValidatorInterface $validator
   *   LightSaml assertion validator.
   */
  public function __construct(AssertionValidatorInterface $validator = NULL) {
    $this->validator = $validator ?: new Validator(
      new NameIdValidator(),
      new SubjectValidator(new NameIdValidator()),
      new StatementValidator()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $message = $context->getMessage();
    if (empty($message->getAllAssertions())) {
        throw new SamlValidationException(
            'The message must contain at least one assertion'
        );
    }

    foreach ($message->getAllAssertions() as $assertion) {
      $this->validator->validateAssertion($assertion);

      if (!$assertion->getIssuer()) {
        throw new SamlValidationException('Assertion must have issuer');
      }

      if ($assertion->getIssuer()->getFormat() !== SamlConstants::NAME_ID_FORMAT_ENTITY) {
        throw new SamlValidationException('Name ID format must be entity');
      }

      if ($assertion->getIssuer()->getValue() !== $identityProvider->getIssuer()) {
        throw new SamlValidationException('Assertion issuer is not known');
      }
    }
  }

}
