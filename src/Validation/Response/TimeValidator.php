<?php

namespace Drupal\saml\Validation\Response;

use Drupal\Component\Datetime\TimeInterface;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface;

class TimeValidator extends ResponseValidatorBase {

  const TIME_SKEW = 120;

  protected $time;

  protected $validator;

  public function __construct(
    TimeInterface $time,
    AssertionTimeValidatorInterface $validator = NULL
  ) {
    $this->time = $time;
    $this->validator = $validator ?: new AssertionTimeValidator();
  }

  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $message = $context->getMessage();

    try {
      $this->validator->validateTimeRestrictions(
        $message->getFirstAssertion(),
        $this->time->getCurrentTime(),
        self::TIME_SKEW
      );
    }
    catch (\Exception $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

}
