<?php

namespace Drupal\lei_core\Field;

use Drupal;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\lei_entity\EntityStorageInterface;

class RestaurantTypeRestaurantsItemList extends Drupal\Core\Field\EntityReferenceFieldItemList
{
  use ComputedItemListTrait;

  /**
   * Computes the values for an item list.
   */
  protected function computeValue()
  {
    $restaurant_ids = [];

    /** @var Drupal\lei_core\Entity\RestaurantTypeInterface $restaurant_type */
    $restaurant_type = $this->getEntity();

    if ($restaurant_type->id()) {
      /** @var EntityStorageInterface $storage */
      $storage = Drupal::entityTypeManager()->getStorage('restaurant');
      $query = $storage->getQuery();
      $query->condition('status', Drupal\lei_core\Entity\RestaurantInterface::PUBLISHED);
      $query->condition('type', $restaurant_type->id());

      $restaurant_ids = $query->execute();
    }

    $offset = 0;

    foreach ($restaurant_ids as $restaurant_id) {
      $this->list[] = $this->createItem($offset++, $restaurant_id);
    }
  }
}
