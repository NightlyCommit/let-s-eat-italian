<?php

namespace Drupal\lei_core;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\lei_core\Entity\ReviewInterface;

/**
 * Defines a class to build a listing of Review entities.
 *
 * @ingroup lei_core
 */
class ReviewListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity ReviewInterface */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.review.canonical',
      ['review' => $entity->id()]
    );

    return $row + parent::buildRow($entity);
  }
}
