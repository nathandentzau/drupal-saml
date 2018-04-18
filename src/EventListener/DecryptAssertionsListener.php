<?php

namespace Drupal\saml\EventListener;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\X509Certificate;
use Drupal\saml\Event\ReceiveSamlMessageEvent;
use Drupal\saml\Exception\SamlValidationException;
use LightSaml\Model\Context\DeserializationContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides an assertion decryption listener.
 */
class DecryptAssertionsListener implements EventSubscriberInterface {

  /**
   * Handle receiving a SAML message.
   *
   * @param Drupal\saml\Event\ReceiveSamlMessageEvent $event
   *   An event object when receiving saml messages.
   */
  public function onReceiveMessage(ReceiveSamlMessageEvent $event) {
    $message = $event->getContext()->asResponse();
    $identityProvider = $event->getIdentityProvider();
    $expectEncryptedResponse = $identityProvider->wantsEncryptedResponse();

    if (!$message || !$expectEncryptedResponse) {
      return;
    }

    $encryptedAssertions = $message->getAllEncryptedAssertions();

    if (empty($encryptedAssertions) && $expectEncryptedResponse) {
      throw new SamlValidationException(
        'Expected assertions to be encrypted but no encrypted assertions found'
      );
    }

    $certificate = new X509Certificate();
    $certificate->loadPem($identityProvider->getEncryptionCertificate());

    $credential = new X509Credential(
      $certificate,
      KeyHelper::createPrivateKey(
        $identityProvider->getEncryptionKey(),
        NULL,
        FALSE,
        $identityProvider->getEncryptionAlgorithm()
      )
    );

    foreach ($encryptedAssertions as $encryptedAssertion) {
      $assertion = $encryptedAssertion->decryptMultiAssertion(
        [$credential],
        new DeserializationContext()
      );

      $message->addAssertion($assertion);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ReceiveSamlMessageEvent::NAME => ['onReceiveMessage', 500],
    ];
  }

}
