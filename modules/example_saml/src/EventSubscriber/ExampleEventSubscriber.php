<?php

namespace Drupal\example_saml\EventSubscriber;

use Drupal\Core\Url;
use LightSaml\SamlConstants;
use LightSaml\Model\Assertion\Attribute;
use Drupal\saml\Event\SamlResponseAlterEvent;
use Drupal\saml\Event\RedirectLocationAlterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExampleEventSubscriber implements EventSubscriberInterface {

  public function onAlterLocation(RedirectLocationAlterEvent $event) {
    if ($event->getIdentityProvider()->id() !== 'example') {
      return;
    }

    $event->setLocation(Url::fromRoute('user.page'));
  }

  public function onAlterSamlResponse(SamlResponseAlterEvent $event) {
    $response = $event->getResponse();
    $account = $event->getAccount();

    $response
      ->getFirstAssertion()
      ->getSubject()
      ->getNameID()
      ->setValue($account->getEmail());

    $response
      ->getFirstAssertion()
      ->getFirstAttributeStatement()
      ->addAttribute(
        (new Attribute())
          ->setName('EmailAddress')
          ->setNameFormat(SamlConstants::ATTRIBUTE_NAME_FORMAT_UNSPECIFIED)
          ->setAttributeValue($account->getEmail())
      );
  }

  public static function getSubscribedEvents() {
    return [
      RedirectLocationAlterEvent::class => 'onAlterLocation',
      SamlResponseAlterEvent::class => 'onAlterSamlResponse',
    ];
  }

}
