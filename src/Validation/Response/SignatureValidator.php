<?php

namespace Drupal\saml\Validation\Response;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\IdentityProviderInterface;
use Drupal\saml\Exception\SamlValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SignatureValidator extends ResponseValidatorBase {

  public function validate(
    MessageContext $context,
    IdentityProviderInterface $identityProvider
  ) {
    $signature = $context->getMessage()->getSignature();

    if (!$signature && $identityProvider->wantsSignedResponse()) {
      throw new SamlValidationException('Expected message to be signed');
    }

    // If the message is not expected to be signed or it is not signed, then do
    // not continue with validation.
    if (!$signature || !$identityProvider->wantsSignedResponse()) {
      return;
    }

    $certificate = new X509Certificate();
    $certificate->loadPem($identityProvider->getResponseCertificate());

    $key = KeyHelper::createPublicKey($certificate);

    try {
      $signature->validate($key);

      foreach ($context->getMessage()->getAllAssertions() as $assertion) {
        if (!$assertion->getSignature() && $identityProvider->getSignedResponse()) {
          throw new SamlValidationException('Expected assertion to be signed');
        }

        $assertion->getSignature()->validate($key);
      }
    }
    catch (\Exception $e) {
      throw new SamlValidationException($e->getMessage());
    }
  }

}
