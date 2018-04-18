<?php

namespace Drupal\saml\Validation\Response;

use Drupal\saml\Validation\ValidatorBase;
use Drupal\saml\Event\ReceiveSamlMessageEvent;

/**
 * Provides a response validator base.
 */
abstract class ResponseValidatorBase extends ValidatorBase {

  /**
   * {@inheritdoc}
   */
  public function onReceiveMessage(ReceiveSamlMessageEvent $event) {
    if (!$event->getContext()->asResponse()) {
      return;
    }

    parent::onReceiveMessage($event);
  }

}
