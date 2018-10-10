<?php

namespace Drupal\saml\Validator\Model\Assertion;

use Drupal\saml\Entity\SamlProviderInterface;
use LightSaml\Validator\Model\NameId\NameIdValidator;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;

/**
 * Provides an abstract class for composite Name ID validators.
 */
abstract class AbstractCompositeNameIdValidator implements NameIdValidatorInterface {

  /**
   * Service Provider.
   *
   * @var Drupal\saml\Entity\SamlProviderInterface
   */
  protected $provider;

  /**
   * Name ID validator.
   *
   * @var LightSaml\Validator\Model\NameId\NameIdValidatorInterface
   */
  protected $validator;

  /**
   * Constructor for AbstractCompositeNameIdValidator.
   *
   * @param Drupal\saml\Entity\SamlProviderInterface $provider
   *   Service Provider.
   * @param LightSaml\Validator\Model\NameId\NameIdValidatorInterface $validator
   *   Name ID validator.
   */
  public function __construct(
    SamlProviderInterface $provider,
    NameIdValidatorInterface $validator = NULL
  ) {
    $this->provider = $provider;
    $this->validator = $validator ?: new NameIdValidator();
  }

  /**
   * Get the Service Provider.
   *
   * @return Drupal\saml\Entity\SamlProviderInterface
   */
  public function getServiceProvider() {
    return $this->provider;
  }

  /**
   * Get the Name ID validator.
   *
   * @return LightSaml\Validator\Model\NameId\NameIdValidatorInterface
   */
  public function getValidator() {
    return $this->validator;
  }

}
