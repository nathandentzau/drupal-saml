<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Event\ReceiveSamlResponseEvent;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use Symfony\Component\HttpFoundation\RequestStack;
use LightSaml\Model\Context\DeserializationContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides an AuthnStatement validator.
 */
class AuthnStatementValidator extends ResponseValidatorBase {

  /**
   * Symfony request.
   *
   * @var Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructor for AuthnStatementValidator.
   *
   * @param Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   A symfony request.
   */
  public function __construct(RequestStack $requestStack) {
    $this->request = $requestStack->getMasterRequest();
  }

  /**
   * {@inheritdoc}
   */
  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $message = $context->getMessage();

    if (!$this->hasAuthnStatement($message->getAllAssertions())) {
      throw new SamlValidationException(
        'Response must have at least one assertion containing AuthnStatement'
      );
    }

    if (!$message->getFirstAssertion()->hasBearerSubject()) {
      throw new SamlValidationException('Response must have bearer subject');
    }

    foreach ($message->getFirstAssertion()->getSubject()->getBearerConfirmations() as $subjectConfirmation) {
      if (!$subjectConfirmation->getSubjectConfirmationData()) {
        throw new SamlValidationException('Bearer SubjectConfirmation must have SubjectConfirmationData element');
      }

      $recipient = $subjectConfirmation->getSubjectConfirmationData()->getRecipient();

      if (!$recipient) {
        throw new SamlValidationException(
          'Bearer SubjectConfirmation must contain Recipient attribute'
        );
      }

      if ($this->request->getUri() !== $recipient) {
        throw new SamlValidationException(
          'Receipient does not match expected value'
        );
      }
    }

    if ($this->hasMessageIdBeenUsed($message->getIssuer()->getValue(), $message->getId())) {
      throw new SamlValidationException('Message has already been received');
    }

    \Drupal::state()->set($this->createStateKey($message->getIssuer()->getValue(), $message->getId()), TRUE);
  }

  protected function hasAuthnStatement(array $assertions) {
    foreach ($assertions as $assertion) {
      if ($assertion->getAllAuthnStatements()) {
        return TRUE;
      }
    }

    return FALSE;
  }

  protected function hasMessageIdBeenUsed($entity_id, $id) {
    return (bool) \Drupal::state()->get($this->createStateKey($entity_id, $id));
  }

  protected function createStateKey($entity_id, $id) {
    $entity_id = sha1($entity_id);
    return "saml_{$entity_id}_{$id}";
  }

}
