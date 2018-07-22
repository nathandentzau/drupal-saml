<?php

namespace Drupal\saml\Validator\Model\Assertion;

use Drupal\Core\Url;
use LightSaml\Model\Assertion\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Entity\IdentityProviderInterface;
use LightSaml\Error\LightSamlValidationException;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use Drupal\saml\Validator\Model\Assertion\SignatureValidator;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;
use LightSaml\Validator\Model\Subject\SubjectValidatorInterface;
use Drupal\saml\Validator\Model\Assertion\CompositeIssuerValidator;
use Drupal\saml\Validator\Model\Assertion\CompositeNameIdValidator;
use Drupal\saml\Validator\Model\Assertion\CompositeSubjectValidator;
use LightSaml\Validator\Model\Statement\StatementValidatorInterface;
use Drupal\saml\Validator\Model\Assertion\CompositeStatementValidator;
use Drupal\saml\Validator\Model\Assertion\SignatureValidatorInterface;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface;
use LightSaml\Validator\Model\Assertion\AssertionValidator as AssertionValidatorBase;

/**
 * Provides an assertion validator.
 */
class AssertionValidator extends AssertionValidatorBase {

  /**
   * Timestamp skew in seconds.
   */
  const TIME_SKEW = 120;

  /**
   * Assertion time validator.
   *
   * @var LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface
   */
  protected $assertionTimeValidator;

  /**
   * Identity provider.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * Signature validator.
   *
   * @var LightSaml\Validator\Model\Subject\SubjectValidatorInterface
   */
  protected $signatureValidator;

  /**
   * Constructor for AssertionValidator.
   *
   * @param Symfony\Component\HttpFoundation\Request $request
   *   Symfony request.
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   Identity provider.
   * @param LightSaml\Validator\Model\NameId\NameIdValidatorInterface $nameIdValidator
   *   Name ID validator.
   * @param LightSaml\Validator\Model\Subject\SubjectValidatorInterface $subjectValidator
   *   Subject validator.
   * @param LightSaml\Validator\Model\Statement\StatementValidatorInterface $statementValidator
   *   Statement validator.
   * @param LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface $assertionTimeValidator
   *   Assertion time validator.
   * @param Drupal\saml\Validator\Model\Assertion\SignatureValidatorInterface $signatureValidator
   *   Signature validator.
   */
  public function __construct(
    Request $request,
    IdentityProviderInterface $identityProvider,
    NameIdValidatorInterface $nameIdValidator = NULL,
    SubjectValidatorInterface $subjectValidator = NULL,
    StatementValidatorInterface $statementValidator = NULL,
    AssertionTimeValidatorInterface $assertionTimeValidator = NULL,
    SignatureValidatorInterface $signatureValidator = NULL
  ) {
    $this->identityProvider = $identityProvider;
    $this->assertionTimeValidator = $assertionTimeValidator
      ?: new AssertionTimeValidator();
    $this->signatureValidator = $signatureValidator
      ?: new SignatureValidator($identityProvider);

    parent::__construct(
      $nameIdValidator ?: new CompositeIssuerValidator($identityProvider),
      $subjectValidator ?: new CompositeSubjectValidator(
        new CompositeNameIdValidator($identityProvider),
        $request
      ),
      $statementValidator ?: new CompositeStatementValidator($identityProvider)
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateAssertion(Assertion $assertion) {
    parent::validateAssertion($assertion);

    $this->validateTime($assertion);
    $this->validateSignature($assertion->getSignature());
  }

  /**
   * {@inheritdoc}
   */
  protected function validateAudienceRestriction(AudienceRestriction $item) {
    parent::validateAudienceRestriction($item);

    $audience = $item->getAllAudience()
      ? $item->getAllAudience()[0]
      : NULL;
    $expectedAudience = Url::fromRoute(
      'saml.inbound',
      ['identityProvider' => $this->identityProvider->id()],
      ['absolute' => TRUE]
    )->toString();

    if ($audience !== $expectedAudience) {
      throw new SamlValidationException(
        sprintf(
          'Audience %s does not match the expected value',
          $audience
        )
      );
    }
  }

  /**
   * Validate the assertion signature.
   *
   * @param LightSaml\Model\XmlDSig\AbstractSignatureReader $signature
   *   A SAML signature reader.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateSignature(AbstractSignatureReader $signature = NULL) {
    if (!$signature) {
      return;
    }

    try {
      $this->signatureValidator->validateSignature($signature);
    }
    catch (LightSamlSecurityException $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

  /**
   * Validation assertion time restrictions.
   *
   * @param LightSaml\Model\Assertion\Assertion $assertion
   *   A SAML assertion.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateTime(Assertion $assertion) {
    try {
      $this
        ->assertionTimeValidator
        ->validateTimeRestrictions(
          $assertion,
          (new \DateTime())->getTimestamp(),
          static::TIME_SKEW
        );
    }
    catch (LightSamlValidationException $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

}
