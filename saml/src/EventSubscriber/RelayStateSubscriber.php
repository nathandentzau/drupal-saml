<?php

namespace Drupal\saml\EventSubscriber;

use Drupal\Core\Url;
use Drupal\saml\Event\RedirectLocationAlterEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\saml\Event\SamlResponseAlterEvent;

/**
 * Provide an event subscriber to handle the RelayState.
 */
class RelayStateSubscriber implements EventSubscriberInterface {

  /**
   * The request stack.
   *
   * @var Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructor for RelayStateSubscriber.
   *
   * @param Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(RequestStack $requestStack) {
    $this->requestStack = $requestStack;
  }

  /**
   * Alter the redirect location if the RelayState is set.
   *
   * @param Drupal\saml\Event\RedirectLocationAlterEvent $event
   *   The redirect location alter event.
   */
  public function onAlterLocation(RedirectLocationAlterEvent $event) {
    $location = $this->requestStack
      ->getMasterRequest()
      ->request
      ->get('RelayState');

    if (!$location) {
      return;
    }

    $event->setLocation(Url::fromUri($location));
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      RedirectLocationAlterEvent::class => ['onAlterLocation', 100],
    ];
  }

}
