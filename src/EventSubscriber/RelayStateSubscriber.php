<?php

namespace Drupal\saml\EventSubscriber;

use Drupal\Core\Url;
use Drupal\saml\Event\RedirectLocationAlterEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\saml\Event\SamlResponseAlterEvent;

class RelayStateSubscriber implements EventSubscriberInterface {

  protected $requestStack;

  public function __construct(RequestStack $requestStack) {
    $this->requestStack = $requestStack;
  }

  public function onAlterLocation(RedirectLocationAlterEvent $event) {
    $location = $this
      ->requestStack
      ->getMasterRequest()
      ->request
      ->get('RelayState');

    if (!$location) {
      return;
    }

    $event->setLocation(Url::fromUri($location));
    $event->stopPropagation();
  }

  public function onAlterSamlResponse(SamlResponseAlterEvent $event) {
    $relayState = $this
      ->requestStack
      ->getMasterRequest()
      ->query
      ->get('redirectTo');

    $event
      ->getResponse()
      ->setRelayState(
        Url::fromUri($relayState)->toString()
      );
  }

  public static function getSubscribedEvents() {
    return [
      RedirectLocationAlterEvent::class => [['onAlterLocation', 1000]],
      SamlResponseAlterEvent::class => [['onAlterSamlResponse', 1000]],
    ];
  }

}
