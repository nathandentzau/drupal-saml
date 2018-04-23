<?php

namespace Drupal\saml\Factory\Model\Metadata;

use LightSaml\SamlConstants;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Event\EntityDescriptorAlterEvent;
use LightSaml\Model\Metadata\AssertionConsumerService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides an entity descriptor factory.
 */
class EntityDescriptorFactory implements EntityDescriptorFactoryInterface {

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
  public function createServiceProvider(
    IdentityProviderInterface $identityProvider
  ) {
    $ssoDescriptor = new SpSsoDescriptor();
    $ssoDescriptor
      ->addAssertionConsumerService(
        (new AssertionConsumerService())
          ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
          ->setLocation($identityProvider->getAssertionConsumerServiceUrl())
      )
      ->setWantAssertionsSigned($identityProvider->wantsSignedResponse());

    if ($identityProvider->wantsEncryptedResponse()) {
      $ssoDescriptor->addKeyDescriptor(
        (new KeyDescriptor())
          ->setUse(KeyDescriptor::USE_ENCRYPTION)
          ->setCertificate(
            (new X509Certificate())
              ->loadPem($identityProvider->getEncryptionCertificate())
          )
      );
    }

    $entityDescriptor = (new EntityDescriptor())
      ->setEntityID($identityProvider->getEntityId())
      ->addItem($ssoDescriptor);

    $event = new EntityDescriptorAlterEvent(
      $entityDescriptor,
      $identityProvider
    );

    $this
      ->eventDispatcher
      ->dispatch(EntityDescriptorAlterEvent::NAME, $event);

    return $event->getEntityDescriptor();
  }

}
