<?php

namespace Drupal\lei_core\Field;

use Drupal;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\lei_core\Entity\RestaurantInterface;
use Drupal\lei_core\Entity\ReviewInterface;
use Drupal\lei_entity\EntityStorageInterface;

class RestaurantReviewsItemList extends Drupal\Core\Field\EntityReferenceFieldItemList
{
  use ComputedItemListTrait;

  /**
   * Computes the values for an item list.
   */
  protected function computeValue()
  {
    $reviewIds = [];

    /** @var RestaurantInterface $restaurant */
    $restaurant = $this->getEntity();

    if ($restaurant->id()) {
      /** @var EntityStorageInterface $reviewStorage */
      $reviewStorage = Drupal::entityTypeManager()->getStorage('review');
      $query = $reviewStorage->getQuery();
      $query->condition('status', ReviewInterface::PUBLISHED);
      $query->condition('restaurant', $restaurant->id());

      $reviewIds = $query->execute();
    }

    $offset = 0;

    foreach ($reviewIds as $reviewId) {
      $this->list[] = $this->createItem($offset++, $reviewId);
    }
  }
}
