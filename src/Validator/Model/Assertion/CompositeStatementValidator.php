<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Model\Assertion\AbstractStatement;
use Drupal\saml\Entity\IdentityProviderInterface;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Model\Assertion\AttributeStatement;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Validator\Model\Statement\StatementValidator;
use LightSaml\Validator\Model\Statement\StatementValidatorInterface;
use LightSaml\SamlConstants;

/**
 * Provides a statement validator.
 */
class CompositeStatementValidator implements StatementValidatorInterface {

  /**
   * Identity Provider.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * Statement validator
   *
   * @var LightSaml\Validator\Model\Statement\StatementValidatorInterface
   */
  protected $validator;

  /**
   * Constructor for CompositeStatementValidator.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   SAML Identity Provider.
   * @param LightSaml\Validator\Model\Statement\StatementValidatorInterface|NULL $validator
   *   Statement validator.
   */
  public function __construct(
    IdentityProviderInterface $identityProvider,
    StatementValidatorInterface $validator = NULL
  ) {
    $this->identityProvider = $identityProvider;
    $this->validator = $validator ?: new StatementValidator();
  }

  /**
   * {@inheritdoc}
   */
  public function validateStatement(AbstractStatement $statement) {
    try {
      $this->validator->validateStatement($statement);
    }
    catch (LightSamlValidationException $e) {
      throw new SamlValidationException($e->getMessage());
    }

    if ($statement instanceof AttributeStatement) {
      return;
    }

    $authnContext = $statement
      ->getAuthnContext()
      ->getAuthnContextClassRef();

    if ($authnContext !== SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT) {
      throw new SamlValidationException(
        sprintf('AuthnContext %s did not match expected value', $authnContext)
      );
    }
  }

}
