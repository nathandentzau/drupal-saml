<?php

namespace Drupal\saml\Validator\Model\Assertion;

use LightSaml\Model\Assertion\AbstractNameID;
use Drupal\saml\Exception\SamlValidationException;

/**
 * Provides a Name ID validator.
 */
class CompositeNameIdValidator extends AbstractCompositeNameIdValidator {

  /**
   * {@inheritdoc}
   */
  public function validateNameId(AbstractNameID $nameId): void {
    try {
      $this
        ->getValidator()
        ->validateNameId($nameId);
    }
    catch (LightSamlValidationException $e) {
      throw new SamlValidationException($e->getMessage());
    }

    $subjectNameIdFormat = $nameId->getFormat();
    $expectedSubjectNameIdFormat = $this
      ->getIdentityProvider()
      ->getNameIdFormat();

    if ($subjectNameIdFormat !== $expectedSubjectNameIdFormat) {
      throw new SamlValidationException(
        sprintf(
          'Subject NameID Format %s did not match expected format',
          $subjectNameIdFormat
        )
      );
    }
  }

}
