<?php

namespace Drupal\saml;

use LightSaml\Helper as LightSamlHelper;
use LightSaml\Credential\KeyHelper;
use LightSaml\Model\Protocol\Response;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\X509Certificate;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use LightSaml\Model\Context\DeserializationContext;

/**
 * Provides a helper class for the module.
 */
final class Helper {

  /**
   * Generate a random identifier for a saml message.
   *
   * @return string
   *   A random identifier with the pattern `drupal_%d`.
   */
  public static function generateId() {
    return sprintf('drupal%s', LightSamlHelper::generateId());
  }

}
