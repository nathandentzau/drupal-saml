<?php

namespace Drupal\saml\HttpFoundation;

use Drupal\Component\HttpFoundation\SecuredRedirectResponse;

/**
 * Provides an external redirect response.
 *
 * Drupal core does not allow you to return a RedirectResponse with an external
 * URL and requires you to return a TrustedRedirectResponse. A
 * TrustedRedirectResponse cannot be returned from a controller because the
 * EarlyRenderingControllerWrapperSubscriber prevents responses that implement
 * CacheableResponseInterface from being returned.
 */
class ExternalRedirectResponse extends SecuredRedirectResponse {

  /**
   * {@inheritdoc}
   */
  protected function isSafe($url) {
    return TRUE;
  }

}
