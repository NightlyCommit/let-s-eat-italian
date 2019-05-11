<?php

namespace Drupal\lei_core\Field;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\lei_core\Entity\RestaurantInterface;

class RestaurantRatingItemList extends FieldItemList
{
  use ComputedItemListTrait;

  /**
   * Computes the values for an item list.
   */
  protected function computeValue()
  {
    $scores = [];

    /** @var RestaurantInterface $restaurant */
    $restaurant = $this->getEntity();
    $reviews = $restaurant->getReviews();

    foreach ($reviews as $review) {
      $scores[] = $review->getScore();
    }

    if (!empty($scores)) {
      $rating = array_sum($scores) / count($scores);

      $this->list[] = $this->createItem(0, $rating);
    }
  }
}
