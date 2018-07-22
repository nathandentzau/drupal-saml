<?php

namespace Drupal\saml\Factory\Model\Protocol;

use Drupal\saml\Helper;
use LightSaml\SamlConstants;
use Drupal\user\UserInterface;
use LightSaml\Credential\KeyHelper;
use LightSaml\Model\Protocol\Status;
use LightSaml\Binding\BindingFactory;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Assertion\Assertion;
use Drupal\Core\Session\AccountInterface;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Assertion\Conditions;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use LightSaml\Model\XmlDSig\SignatureWriter;
use Drupal\saml\Event\SamlResponseAlterEvent;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Event\ReceiveSamlMessageEvent;
use Drupal\saml\Entity\ServiceProviderInterface;
use Drupal\saml\Entity\IdentityProviderInterface;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use Drupal\saml\Builder\Model\Protocol\ResponseBuilder;
use LightSaml\Model\Assertion\EncryptedAssertionWriter;
use Drupal\saml\Validator\Model\Protocol\ResponseValidator;
use Drupal\saml\Validator\Model\Protocol\SamlResponseValidator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a saml message factory.
 */
class SamlResponseFactory implements SamlMessageFactoryInterface {

  /**
   * SAML binding factory.
   *
   * @var LightSaml\Binding\BindingFactory;
   */
  protected $bindingFactory;

  /**
   * Undocumented variable
   *
   * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructor for SamlMessageFactory.
   *
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   Symfony event dispatcher.
   * @param LightSaml\Binding\BindingFactory $bindingFactory
   *   SAML binding factory.
   */
  public function __construct(
    EventDispatcherInterface $eventDispatcher,
    BindingFactory $bindingFactory = NULL
  ) {
    $this->bindingFactory = $bindingFactory ?: new BindingFactory();
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function create(
    ServiceProviderInterface $serviceProvider,
    AccountInterface $account,
    \DateTime $currentTime = NULL
  ) {
    $currentTime = $currentTime ?: new \DateTime();
    $issuer = (new Issuer())
      ->setFormat($serviceProvider->getIssuerFormat())
      ->setValue($serviceProvider->getIssuer());
    $status = (new Status())
      ->setSuccess();
    $signature = NULL;

    if ($serviceProvider->wantsSignedResponse()) {
      $certificiate = (new X509Certificate())
        ->loadPem($serviceProvider->getSignatureCertificate());
      $privateKey = KeyHelper::createPrivateKey(
        $serviceProvider->getSignatureKey(),
        NULL,
        FALSE,
        XMLSecurityKey::RSA_SHA256
      );

      $signature = SignatureWriter::createByKeyAndCertificate(
        $certificiate,
        $privateKey
      );
    }

    $currentTime = new \DateTime();

    $subjectNameId = (new NameID())
      ->setFormat(SamlConstants::NAME_ID_FORMAT_UNSPECIFIED)
      ->setValue($account->getEmail());
    $subjectConfirmationData = (new SubjectConfirmationData())
      ->setNotBefore($currentTime->modify('-5 minutes'))
      ->setNotOnOrAfter($currentTime->modify('+5 minutes'))
      ->setRecipient($serviceProvider->getAssertionConsumerServiceUrl());
    $subjectConfirmation = (new SubjectConfirmation())
      ->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER)
      ->setSubjectConfirmationData($subjectConfirmationData);
    $subject = (new Subject())
      ->setNameID($subjectNameId)
      ->addSubjectConfirmation($subjectConfirmation);

    $audienceRestriction = (new AudienceRestriction())
      ->addAudience($serviceProvider->getAudienceRestriction());
    $conditions = (new Conditions())
      ->setNotBefore($currentTime->modify('-5 minutes'))
      ->setNotOnOrAfter($currentTime->modify('+5 minutes'))
      ->addItem($audienceRestriction);

    $attributeStatement = new AttributeStatement();

    $assertion = (new Assertion())
      ->setId(Helper::generateId())
      ->setIssueInstant($currentTime)
      ->setIssuer($issuer)
      ->setSubject($subject)
      ->setConditions($conditions)
      ->addItem($attributeStatement);

    $response = (new Response())
      ->setID(Helper::generateId())
      ->setIssueInstant($currentTime)
      ->setDestination($serviceProvider->getAssertionConsumerServiceUrl())
      ->setIssuer($issuer)
      ->setStatus($status)
      ->addAssertion($assertion);

    if ($signature) {
      $response->setSignature($signature);
    }

    $event = new SamlResponseAlterEvent($response, $serviceProvider, $account);
    $this
      ->eventDispatcher
      ->dispatch(SamlResponseAlterEvent::class, $event);

    $response = $event->getResponse();

    if ($serviceProvider->wantsEncryptedResponse()) {
      $assertions = $response->getAllAssertions();

      foreach ($assertions as $assertion) {
        $certificate = (new X509Certificate())
          ->loadPem($serviceProvider->getEncryptionCertificate());
        $publicKey = KeyHelper::createPublicKey($certificate);

        $encryptedAssertion = new EncryptedAssertionWriter(
          XMLSecurityKey::AES256_CBC,
          XMLSecurityKey::RSA_SHA256
        );
        $encryptedAssertion->encrypt($assertion, $publicKey);

        $response->addEncryptedAssertion($encryptedAssertion);
        $response->removeAssertion($assertion);
      }
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function createFromRequest(
    IdentityProviderInterface $identityProvider,
    Request $request
  ) {
    $context = new MessageContext();
    $context->setBindingType(
      $this->bindingFactory->detectBindingType($request)
    );

    $binding = $this->bindingFactory->getBindingByRequest($request);
    $binding->receive($request, $context);

    $message = $context->getMessage();

    if ($message instanceof Response) {
      if (!empty($message->getAllEncryptedAssertions())) {
        Helper::decryptAssertions(
          $message,
          $identityProvider->getEncryptionCertificate(),
          $identityProvider->getEncryptionKey(),
          $identityProvider->getEncryptionAlgorithm()
        );
      }

      (new SamlResponseValidator($identityProvider, $request))
        ->validate($context);
    }

    $this->eventDispatcher->dispatch(
      ReceiveSamlMessageEvent::NAME,
      new ReceiveSamlMessageEvent($context, $identityProvider)
    );

    return $message;
  }

}
