<?php

namespace Drupal\saml;

use LightSaml\Binding\BindingFactory;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;
use Drupal\saml\Event\ReceiveSamlMessageEvent;
use Drupal\saml\Entity\IdentityProviderInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a saml message factory.
 */
class SamlMessageFactory {

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
   * Create a SAML message from the HTTP request.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   The Identity Provider entity.
   * @param Symfony\Component\HttpFoundation\Request $request
   *   The symfony http request.
   *
   * @return LightSaml\Model\Protocol\SamlMessage|null
   *   The SAML message.
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

    $this->eventDispatcher->dispatch(
      ReceiveSamlMessageEvent::NAME,
      new ReceiveSamlMessageEvent($context, $identityProvider)
    );

    return $context->getMessage();
  }

}
