<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Model\Assertion\Subject;
use Symfony\Component\HttpFoundation\Request;
use LightSaml\Error\LightSamlValidationException;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Validator\Model\NameId\NameIdValidator;
use LightSaml\Validator\Model\Subject\SubjectValidator;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use LightSaml\Validator\Model\Subject\SubjectValidatorInterface;

/**
 * Provides an assertion subject validator.
 */
class CompositeSubjectValidator implements SubjectValidatorInterface {

  /**
   * Name ID validator.
   *
   * @var LightSaml\Validator\Model\NameId\NameIdValidatorInterface
   */
  protected $nameIdValidator;

  /**
   * Assertion subject validator.
   *
   * @var LightSaml\Validator\Model\Subject\SubjectValidatorInterface
   */
  protected $subjectValidator;

  /**
   * Symfony request.
   *
   * @var Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructor for CompositeSubjectValidator.
   *
   * @param LightSaml\Validator\Model\NameId\NameIdValidatorInterface $nameIdValidator
   *   Name ID validator.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   Symfony request.
   * @param LightSaml\Validator\Model\Subject\SubjectValidatorInterface $subjectValidator
   *   Subject validator.
   */
  public function __construct(
    NameIdValidatorInterface $nameIdValidator,
    Request $request,
    SubjectValidatorInterface $subjectValidator = NULL
  ) {
    $this->nameIdValidator = $nameIdValidator;
    $this->request = $request;
    $this->subjectValidator = $subjectValidator
      ?: new SubjectValidator($nameIdValidator);
  }

  /**
   * {@inheritdoc}
   */
  public function validateSubject(Subject $subject) {
    try {
      $this->subjectValidator->validateSubject($subject);
    }
    catch (LightSamlValidationException $e) {
      throw new SamlValidationException($e->getMessage());
    }

    if (empty($subject->getBearerConfirmations())) {
      throw new SamlValidationException(
        'Subject must container a bearer confirmation'
      );
    }

    $bearerConfirmations = $subject->getBearerConfirmations();

    foreach ($bearerConfirmations as $subjectConfirmation) {
      $recipient = $subjectConfirmation
        ->getSubjectConfirmationData()
        ->getRecipient();
      $expectedRecipient = $this
        ->request
        ->getUri();

      if ($recipient !== $expectedRecipient) {
        throw new SamlValidationException(
          sprintf('Recipient %s does not match expected value', $recipient)
        );
      }
    }
  }

}
