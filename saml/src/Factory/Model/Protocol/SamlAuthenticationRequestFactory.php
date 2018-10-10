<?php

namespace Drupal\saml\Factory\Model\Protocol;

use Drupal\Core\Url;
use Drupal\saml\Helper;
use LightSaml\SamlConstants;
use LightSaml\Model\Assertion\Issuer;
use Drupal\saml\Entity\ServiceProvider;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Protocol\AuthnRequest;
use Drupal\saml\Event\SamlMessageAlterEvent;
use Drupal\saml\Entity\SamlProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LightSaml\Credential\KeyHelper;
use LightSaml\Model\XmlDSig\SignatureWriter;

/**
 * Provides a saml authentication request factory.
 */
class SamlAuthenticationRequestFactory implements SamlMessageFactoryInterface {

  /**
   * The event dispatcher.
   *
   * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructor for SamlAuthenticationRequestFactory.
   *
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   */
  public function __construct(EventDispatcherInterface $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function create(SamlProviderInterface $provider) {
    if (!$provider instanceof ServiceProvider) {
      throw new \InvalidArgumentException(
        'Provider entity must be an Service Provider'
      );
    }

    $authnRequest = new AuthnRequest();
    $authnRequest->setAssertionConsumerServiceURL(
      Url::fromRoute(
        'saml.service_provider.consume',
        ['serviceProvider' => $provider->id()]
      )
      ->setAbsolute(TRUE)
      ->toString()
    );
    $authnRequest->setProtocolBinding(SamlConstants::BINDING_SAML2_HTTP_POST);
    $authnRequest->setID(Helper::generateId());
    $authnRequest->setIssueInstant(new \DateTime());
    $authnRequest->setDestination($provider->getSingleSignOnUrl());
    $authnRequest->setIssuer(
      new Issuer(
        Url::fromRoute(
          'saml.service_provider',
          ['serviceProvider' => $provider->id()]
        )
        ->setAbsolute(TRUE)
        ->toString()
      )
    );

    if ($provider->getSignedRequest()) {
      $certificate = (new X509Certificate())->loadPem(
        $provider->getSignatureRequestCertificate()
      );
      $key = KeyHelper::createPrivateKey(
        $provider->getSignatureRequestKey(),
        NULL
      );

      $authnRequest->setSignature(new SignatureWriter($certificate, $key));
    }

    $event = new SamlMessageAlterEvent($authnRequest, $provider);
    $this->eventDispatcher->dispatch(SamlMessageAlterEvent::class, $event);

    return $event->getMessage();
  }

  /**
   * {@inheritdoc}
   */
  public function createFromRequest(
    SamlProviderInterface $serviceProvider,
    Request $request
  ) {
    return new AuthnRequest();
  }

}
