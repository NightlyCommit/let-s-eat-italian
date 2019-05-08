<?php

namespace Drupal\lei_core\Entity\Annotation;

use Drupal\Core\Entity\Annotation\ContentEntityType;

/**
 * Defines a LEI entity type annotation object.
 *
 * @ingroup entity_api
 *
 * @Annotation
 */
class LEIEntityType extends ContentEntityType
{
  /**
   * {@inheritdoc}
   */
  public $entity_type_class = 'Drupal\lei_core\Entity\LEIEntityType';
}
