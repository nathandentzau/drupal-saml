<?php

namespace Drupal\saml\Factory\Model\Protocol;

use LightSaml\Binding\BindingFactory;
use LightSaml\Model\Protocol\Response;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Event\ReceiveSamlMessageEvent;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Validator\Model\Protocol\ResponseValidator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a saml message factory.
 */
class SamlMessageFactory implements SamlMessageFactoryInterface {

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

      (new ResponseValidator($identityProvider, $request))->validate($context);
    }

    $this->eventDispatcher->dispatch(
      ReceiveSamlMessageEvent::NAME,
      new ReceiveSamlMessageEvent($context, $identityProvider)
    );

    return $message;
  }

}
