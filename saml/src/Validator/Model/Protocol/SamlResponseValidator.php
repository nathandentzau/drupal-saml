<?php

namespace Drupal\saml\Validator\Model\Protocol;

use LightSaml\SamlConstants;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\Response;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\Assertion\AbstractNameID;
use Symfony\Component\HttpFoundation\Request;
use LightSaml\Error\LightSamlSecurityException;
use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Error\LightSamlValidationException;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use Drupal\saml\Validator\Model\Assertion\AssertionValidator;
use Drupal\saml\Validator\Model\Assertion\SignatureValidator;
use Drupal\saml\Validator\Model\Assertion\CompositeIssuerValidator;
use LightSaml\Validator\Model\Assertion\AssertionValidatorInterface;
use Drupal\saml\Validator\Model\Assertion\SignatureValidatorInterface;

/**
 * Provides a SAML Response validator.
 */
class SamlResponseValidator implements SamlMessageValidatorInterface {

  /**
   * The Service Provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $provider;

  /**
   * The current request.
   *
   * @var Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Assertion validator.
   *
   * @var Drupal\saml\Validator\Model\Assertion\AssertionValidator
   */
  protected $assertionValidator;

  /**
   * Issuer validator.
   *
   * @var Drupal\saml\Validator\Model\Assertion\CompositeIssuerValidator
   */
  protected $issuerValidator;

  /**
   * Signature validator.
   *
   * @var Drupal\saml\Validator\Model\Assertion\SignatureValidator
   */
  protected $signatureValidator;

  /**
   * Constructor for SamlResponseValidator.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   The Service Provider.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param Drupal\saml\Validator\Model\Assertion\AssertionValidator $assertionValidator
   *   Assertion validator.
   * @param Drupal\saml\Validator\Model\Assertion\CompositeIssuerValidator $issuerValidator
   *   Issuer validator.
   * @param Drupal\saml\Validator\Model\Assertion\SignatureValidator $signatureValidator
   *   Signature validator.
   */
  public function __construct(
    SamlProviderInterface $provider,
    Request $request,
    AssertionValidatorInterface $assertionValidator = NULL,
    CompositeIssuerValidator $issuerValidator = NULL,
    SignatureValidatorInterface $signatureValidator = NULL
  ) {
    $this->provider = $provider;
    $this->request = $request;
    $this->assertionValidator = $assertionValidator
      ?: new AssertionValidator($request, $provider);
    $this->issuerValidator = $issuerValidator
      ?: new CompositeIssuerValidator($provider);
    $this->signatureValidator = $signatureValidator
      ?: new SignatureValidator($provider);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(MessageContext $context) {
    $bindingType = $context->getBindingType();
    $message = $context->getMessage();
    $assertions = $message->getAllAssertions();

    $this->validateBindingType($bindingType);
    $this->validateDestination($message->getDestination());
    $this->validateEncryptedAssertions($message);
    $this->validateIssuer($message->getIssuer());
    $this->validateSignature($message->getSignature());
    $this->validateStatus($message->getStatus());
    $this->validateVersion($message->getVersion());
    $this->validateAssertions($assertions);
  }

  /**
   * Validate assertions.
   *
   * @param Assertion[] $assertions
   *   A list of assertions to be validated.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateAssertions(array $assertions) {
    if (empty($assertions)) {
        throw new SamlValidationException(
            'Message must contain at least one (1) Assertion'
        );
    }

    $hasAuthnStatement = function (array $assertions) {
      foreach ($assertions as $assertion) {
        if (!empty($assertion->getAllAuthnStatements())) {
          return TRUE;
        }
      }

      return FALSE;
    };

    if (!$hasAuthnStatement($assertions)) {
      throw new SamlValidationException(
        'Message must contain at least one (1) AuthnStatement'
      );
    }

    $hasAttributeStatement = function (array $assertions) {
      foreach ($assertions as $assertion) {
        if (!empty($assertion->getAllAttributeStatements())) {
          return TRUE;
        }
      }

      return FALSE;
    };

    if (!$hasAttributeStatement($assertions)) {
      throw new SamlValidationException(
        'Message must contain at least one (1) AttributeStatement'
      );
    }

    foreach ($assertions as $assertion) {
      try {
        $this->assertionValidator->validateAssertion($assertion);
      }
      catch (\Exception $e) {
        throw new SamlValidationException($e->getMessage());
      }
    }

    $assertionIssuer = $assertion
      ->getIssuer()
      ->getValue();

    if ($assertionIssuer !== $this->provider->getIssuer()) {
      throw new SamlValidationException(
        sprintf(
          'Expected Assertion Issuer did not match %s',
          $assertionIssuer
        )
      );
    }
  }

  /**
   * Validate the binding type.
   *
   * @param string $bindingType
   *   The binding type.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateBindingType($bindingType) {
    $expectedBindingType = SamlConstants::BINDING_SAML2_HTTP_POST;

    if ($bindingType !== $expectedBindingType) {
      throw new SamlValidationException(
        sprintf(
          'Binding type must be %s',
          SamlConstants::BINDING_SAML2_HTTP_POST
        )
      );
    }
  }

  /**
   * Validate the destination.
   *
   * @param string $destination
   *   The destination of the SAML message.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateDestination($destination) {
    $expectedDestination = $this
      ->request
      ->getUri();

    if ($destination !== $expectedDestination) {
      throw new SamlValidationException(
        'Destination % does not match expected value',
        $destination
      );
    }
  }

  /**
   * Validate encrypted assertions.
   *
   * @param LightSaml\Model\Protocol\Response $message
   *   The SAML Response message.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateEncryptedAssertions(Response $message) {
    $wantsEncryptedResponse = $this
      ->provider
      ->getEncryptedResponse();

    if ($wantsEncryptedResponse && !$message->getAllEncryptedAssertions()) {
      throw new SamlValidationException(
        'Expected assertions to be encrypted but none found'
      );
    }
  }

  /**
   * Validate issuer.
   *
   * @param LightSaml\Model\Assertion\AbstractNameID $issuer
   *   The NameID element.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateIssuer(AbstractNameID $issuer) {
    try {
      $this
        ->issuerValidator
        ->validateNameId($issuer);
    }
    catch (LightSamlValidationException $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

  /**
   * Validate signature.
   *
   * @param LightSaml\Model\XmlDSig\AbstractSignatureReader $signature
   *   The signature to be validated.
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
   * Validate status.
   *
   * @param LightSaml\Model\Protocol\Status $status
   *   The status to be validated.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateStatus(Status $status) {
    if (!$status->isSuccess()) {
      throw new SamlValidationException(
        sprintf('Response is not %s', $status->getStatusCode())
      );
    }
  }

  /**
   * Validate SAML version.
   *
   * @param string $version
   *   The SAML version to be validated.
   *
   * @throws Drupal\saml\Exception\SamlValidationException
   */
  protected function validateVersion($version) {
    if ($version !== SamlConstants::VERSION_20) {
      throw new SamlValidationException('SAML version 2.0 supported only');
    }
  }

}
