<?php

namespace Drupal\saml\EventSubscriber;

use Drupal\saml\Event\ProvisionUserEvent;
use Drupal\saml\Field\FieldMapperManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides a user field mapper subscriber.
 */
class UserFieldMapperSubscriber implements EventSubscriberInterface {

  /**
   * User field mapper manager.
   *
   * @var Drupal\saml\Field\FieldMapperManagerInterface
   */
  protected $fieldMapperManager;

  /**
   * Constructor for UserFieldMapperSubscriber.
   *
   * @param FieldMapperManagerInterface $fieldMapperManager
   */
  public function __construct(FieldMapperManagerInterface $fieldMapperManager) {
    $this->fieldMapperManager = $fieldMapperManager;
  }

  /**
   * Map SAML attribute values to Drupal field values on a user.
   *
   * @param Drupal\saml\Event\ProvisionUserEvent $event
   *   The provision user event.
   */
  public function onProvisionUser(ProvisionUserEvent $event) {
    $serviceProvider = $event->getServiceProvider();
    $mappers = $this->fieldMapperManager->loadByProvider($serviceProvider);

    if (empty($mappers)) {
      return;
    }

    $message = $event->getMessage();
    $attributeStatement = $message
      ->getFirstAssertion()
      ->getFirstAttributeStatement();

    $account = $event->getAccount();

    foreach ($mappers as $mapper) {
      $attribute = $attributeStatement->getFirstAttributeByName(
        $mapper->getAttributeName()
      );

      $mapper->validateValue($attribute);

      $account->set(
        $mapper->getFieldName(),
        $mapper->buildValue($attribute)
      );
    }

    $account->save();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ProvisionUserEvent::class => 'onProvisionUser',
    ];
  }

}
