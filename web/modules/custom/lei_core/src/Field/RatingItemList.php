<?php

namespace Drupal\lei_core\Field;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\lei_core\Entity\RestaurantInterface;

class RatingItemList extends FieldItemList
{
  use ComputedItemListTrait;

  /**
   * Computes the values for an item list.
   */
  protected function computeValue()
  {
    /** @var RestaurantInterface $entity */
    $entity = $this->getEntity();

    $this->list[] = $this->createItem(0, 3.757);
  }
}
