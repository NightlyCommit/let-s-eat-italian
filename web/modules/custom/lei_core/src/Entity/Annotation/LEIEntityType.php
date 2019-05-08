<?php

namespace Drupal\lei_core\Entity\Annotation;

use Drupal\Core\Config\Entity\ConfigEntityType;
use Drupal\Core\Entity\Annotation\ContentEntityType;

/**
 * Defines a LEI entity type annotation object.
 *
 * @Annotation
 */
class LEIEntityType extends ContentEntityType {

  /**
   * {@inheritdoc}
   */
  public $entity_type_class = ConfigEntityType::class;
}
