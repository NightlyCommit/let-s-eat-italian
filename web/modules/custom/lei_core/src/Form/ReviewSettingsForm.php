<?php

namespace Drupal\lei_core\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class ReviewSettingsForm.
 *
 * @ingroup lei_core
 */
class ReviewSettingsForm extends EntityTypeSettingsFormBase {
  public function __construct(EntityTypeManagerInterface $entity_type_manager)
  {
    parent::__construct($entity_type_manager, 'review');
  }
}
