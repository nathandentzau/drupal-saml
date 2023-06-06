<?php

namespace Drupal\saml\Factory\Model\Protocol;

use LightSaml\Credential\KeyHelper;
use LightSaml\Binding\BindingFactory;
use LightSaml\Model\Protocol\Response;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\X509Certificate;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Event\ReceiveSamlMessageEvent;
use LightSaml\Model\Context\DeserializationContext;
use Drupal\saml\Validator\Model\Protocol\SamlResponseValidator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a saml response factory.
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
  public function create(SamlProviderInterface $provider) {
    return new Response();
  }

  /**
   * {@inheritdoc}
   */
  public function createFromRequest(
    SamlProviderInterface $serviceProvider,
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
        $this->decryptAssertions(
          $message,
          $serviceProvider->getEncryptionResponseCertificate(),
          $serviceProvider->getEncryptionResponseKey(),
          $serviceProvider->getEncryptionResponseAlgorithm()
        );
      }

      (new SamlResponseValidator($serviceProvider, $request))
        ->validate($context);
    }

    $this->eventDispatcher->dispatch(
      new ReceiveSamlMessageEvent($context, $serviceProvider)
    );

    return $message;
  }

  /**
   * Decrypt SAML assertions.
   *
   * @param LightSaml\Model\Protocol\Response $message
   *   SAML Response message.
   * @param string $certificate
   *   X509 certificate.
   * @param string $key
   *   RSA private key.
   * @param string $encryptionAlgorithm
   *   XML encryption algorithm.
   */
  protected function decryptAssertions(
    Response $message,
    $certificate,
    $key,
    $encryptionAlgorithm = XMLSecurityKey::RSA_SHA256
  ) {
    $certificate = (new X509Certificate())->loadPem($certificate);
    $key = KeyHelper::createPrivateKey($key, NULL, FALSE, $encryptionAlgorithm);
    $credential = new X509Credential($certificate, $key);

    foreach ($message->getAllEncryptedAssertions() as $encryptedAssertion) {
      $assertion = $encryptedAssertion->decryptMultiAssertion(
        [$credential],
        new DeserializationContext()
      );

      $message->addAssertion($assertion);
    }
  }

}
