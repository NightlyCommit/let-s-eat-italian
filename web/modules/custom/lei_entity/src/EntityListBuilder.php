<?php

namespace Drupal\lei_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Review entities.
 *
 * @ingroup lei_entity
 */
class EntityListBuilder extends \Drupal\Core\Entity\EntityListBuilder
{
  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['name'] = $this->t('Name');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {
    $entityTypeId = $entity->getEntityTypeId();

    $row['name'] = Link::createFromRoute(
      $entity->label(),
      "entity.{$entityTypeId}.canonical",
      [
        'entity' => $entity->id()
      ]
    );

    return $row + parent::buildRow($entity);
  }
}
