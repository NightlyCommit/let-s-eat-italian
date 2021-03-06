<?php

namespace Drupal\lei_entity\Entity\Annotation;

use Drupal\Core\Entity\Annotation\ContentEntityType;

/**
 * Defines a LEI entity type annotation object.
 *
 * @ingroup lei_entity
 *
 * @Annotation
 */
class LEIEntityType extends ContentEntityType
{
  /**
   * {@inheritdoc}
   */
  public $entity_type_class = 'Drupal\lei_entity\Entity\EntityType';
}
