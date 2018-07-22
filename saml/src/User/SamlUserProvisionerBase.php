<?php

namespace Drupal\saml\User;

use LightSaml\Model\Protocol\Response;
use Drupal\saml\Event\ProvisionSamlUserEvent;
use Drupal\saml\Attribute\AttributeFieldMapInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class SamlUserProvisionerBase implements EventSubscriberInterface, ContainerAwareInterface {

  protected $container;

  public function __construct(ContainerInterface $container = NULL) {
    $this->container = $container;
  }

  public function onProvisionUser(ProvisionSamlUserEvent $event): void {
    $identityProvider = $event->getIdentityProvider();

    if ($identityProvider->id() !== $this->getIdentityProviderIdentifer()) {
      return; // bail, this is not on IdP you're looking for...
    }

    $this->doProvisionUser(
      $event->getAccount(),
      $identityProvider,
      $event->getMessage()
    );
  }

  public function getContainer(): ContainerInterface {
    if (!$this->container) {
      $this->container = \Drupal::getContainer();
    }

    return $this->container;
  }

  public function setContainer(ContainerInterface $container = NULL) {
    $this->container = $container;
    return $this;
  }

  protected function doProvisionUser(
    AccountInterface $account,
    IdentityProviderInterface $identityProvider,
    Response $message
  ): void {
    $attributeStatement = $message
      ->getFirstAssertion()
      ->getFirstAttributeStatement();
    $attributeFieldMapClasses = $this->getAttributeFieldMapClasses();

    foreach ($attributeFieldMaps as $attributeFieldMapClass) {
      $attributeFieldMap = $this
        ->instantiateAttributeFieldMap($attributeFieldMapClass);
      $rawAttribute = $attributeStatement
        ->getFirstAttributeByName($attributeFieldMap->getAttributeName());

      $attributeFieldMap->validateAttribute($rawAttribute);

      $account->set(
        $attributeFieldMap->getFieldName(),
        $attributeFieldMap->normalizeAttributeValue($rawAttribute)
      );
    }

    $account->save();
  }

  protected function instantiateAttributeFieldMap(
    string $attributeFieldMapClass
  ): AttributeFieldMapInterface {
    if (!is_subclass_of($attributeFieldMapClass, AttributeFieldMapInterface::class, TRUE)) {
      throw new \InvalidArgumentException(
        '$attributeFieldMapClass must be an instance of ' . AttributeFieldMapInterface::class
      );
    }

    if (is_subclass_of($attributeFieldMapClass, ContainerInjectionInterface::class, TRUE)) {
      return $attributeFieldMapClass::create($this->container);
    }

    return new $attributeFieldMapClass();
  }

  protected function getAttributeFieldMapClasses(): array {
    return [];
  }

  abstract protected function getIdentityProviderIdentifier(): string;

  public static function getSubscribedEvents(): array {
    return [
      ProvisionSamlUserEvent::class => 'onProvisionUser',
    ];
  }

}
