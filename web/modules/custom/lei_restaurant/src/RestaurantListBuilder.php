<?php

namespace Drupal\lei_restaurant;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Restaurant entities.
 *
 * @ingroup lei_restaurant
 */
class RestaurantListBuilder extends EntityListBuilder {


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
    /* @var $entity \Drupal\lei_restaurant\Entity\Restaurant */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.restaurant.canonical',
      ['restaurant' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
