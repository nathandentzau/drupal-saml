<?php

namespace Drupal\saml\Validator\Model\Assertion;

use Drupal\saml\Entity\IdentityProviderInterface;
use LightSaml\Validator\Model\NameId\NameIdValidator;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;

/**
 * Provides an abstract class for composite Name ID validators.
 */
abstract class AbstractCompositeNameIdValidator implements NameIdValidatorInterface {

  /**
   * Identity provider.
   *
   * @var Drupal\saml\Entity\IdentityProviderInterface
   */
  protected $identityProvider;

  /**
   * Name ID validator.
   *
   * @var LightSaml\Validator\Model\NameId\NameIdValidatorInterface
   */
  protected $validator;

  /**
   * Constructor for AbstractCompositeNameIdValidator.
   *
   * @param Drupal\saml\Entity\IdentityProviderInterface $identityProvider
   *   Identity provider.
   * @param LightSaml\Validator\Model\NameId\NameIdValidatorInterface $validator
   *   Name ID validator.
   */
  public function __construct(
    IdentityProviderInterface $identityProvider,
    NameIdValidatorInterface $validator = NULL
  ) {
    $this->identityProvider = $identityProvider;
    $this->validator = $validator ?: new NameIdValidator();
  }

  /**
   * Get the identity provider.
   *
   * @return Drupal\saml\Entity\IdentityProviderInterface
   */
  public function getIdentityProvider() {
    return $this->identityProvider;
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
