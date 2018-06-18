<?php

namespace Drupal\saml\Entity;

use Drupal\Core\Url;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Provides an Service Provider configuration entity.
 *
 * @ConfigEntityType(
 *   id = "service_provider",
 *   label = @Translation("Service Provider"),
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   }
 * )
 */
class ServiceProvider extends ConfigEntityBase implements ServiceProviderInterface {

  use SamlProviderTrait;

  /**
   * Assertion Consumer Service URL.
   *
   * @var string
   */
  protected $acs_url;

  /**
   * AuthnContext Audience Restriction
   *
   * @var string
   */
  protected $audience_restriction;

  /**
   * Signature key.
   *
   * @var string
   */
  protected $signature_key;

  /**
   * {@inheritdoc}
   */
  public function getAssertionConsumerServiceUrl() {
    return $this->acs_url;
  }

  /**
   * {@inheritdoc}
   */
  public function getAudienceRestriction() {
    return $this->audience_restriction;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureKey() {
    return $this->signature_key;
  }

  /**
   * {@inheritdoc}
   */
  public function getIssuer() {
    return Url::fromRoute(
      'saml.outbound',
      ['serviceProvider' => $this->id()],
      ['absolute' => TRUE]
    )->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataUrl() {
    return Url::fromRoute(
      'saml.idp.metadata',
      ['serviceProvider' => $this->id()],
      ['absolute' => TRUE]
    )->toString();
  }

}
