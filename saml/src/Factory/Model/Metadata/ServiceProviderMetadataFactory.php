<?php

namespace Drupal\saml\Factory\Model\Metadata;

use Drupal\Core\Url;
use LightSaml\SamlConstants;
use LightSaml\Credential\KeyHelper;
use Drupal\saml\Entity\ServiceProvider;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\XmlDSig\SignatureWriter;
use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use Drupal\saml\Event\EntityDescriptorAlterEvent;
use LightSaml\Model\Metadata\AssertionConsumerService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a service provider metadata factory.
 */
class ServiceProviderMetadataFactory implements SamlMetadataFactoryInterface {

  /**
   * Symfony event dispatcher.
   *
   * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructor for EntityDescriptorFactory.
   *
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   Symfony event dispatcher.
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

    $assertionConsumerServiceUrl = Url::fromRoute(
        'saml.service_provider.consume',
        ['serviceProvider' => $provider->id()]
      )
      ->setAbsolute(TRUE)
      ->toString();

    $ssoDescriptor = new SpSsoDescriptor();
    $ssoDescriptor
      ->addAssertionConsumerService(
        (new AssertionConsumerService())
          ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
          ->setLocation($assertionConsumerServiceUrl)
      )
      ->setWantAssertionsSigned($provider->getSignedResponse())
      ->setAuthnRequestsSigned($provider->getSignedRequest())
      ->addNameIDFormat($provider->getNameIdFormat());

    if ($provider->getEncryptedResponse()) {
      $ssoDescriptor->addKeyDescriptor(
        (new KeyDescriptor())
          ->setUse(KeyDescriptor::USE_ENCRYPTION)
          ->setCertificate(
            (new X509Certificate())
              ->loadPem($provider->getEncryptionCertificate())
          )
      );
    }

    if ($provider->getSignedRequest()) {
      $certificate = (new X509Certificate())->loadPem(
        $provider->getSignatureRequestCertificate()
      );
      $key = KeyHelper::createPrivateKey(
        $provider->getSignatureRequestKey(),
        NULL
      );

      $ssoDescriptor->addSignature(new SignatureWriter($certificate, $key));
    }

    $entityId = Url::fromRoute(
        'saml.service_provider',
        ['serviceProvider' => $provider->id()]
      )
      ->setAbsolute(TRUE)
      ->toString();

    $entityDescriptor = (new EntityDescriptor())
      ->setEntityID($entityId)
      ->addItem($ssoDescriptor);

    $event = new EntityDescriptorAlterEvent(
      $entityDescriptor,
      $provider
    );

    $this
      ->eventDispatcher
      ->dispatch(EntityDescriptorAlterEvent::class, $event);

    return $event->getEntityDescriptor();
  }

}
