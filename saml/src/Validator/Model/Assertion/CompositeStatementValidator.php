<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Model\Assertion\AbstractStatement;
use Drupal\saml\Entity\SamlProviderInterface;
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
   * Service Provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $provider;

  /**
   * Statement validator
   *
   * @var LightSaml\Validator\Model\Statement\StatementValidatorInterface
   */
  protected $validator;

  /**
   * Constructor for CompositeStatementValidator.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   SAML Service Provider.
   * @param LightSaml\Validator\Model\Statement\StatementValidatorInterface|NULL $validator
   *   Statement validator.
   */
  public function __construct(
    SamlProviderInterface $provider,
    StatementValidatorInterface $validator = NULL
  ) {
    $this->provider = $provider;
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
