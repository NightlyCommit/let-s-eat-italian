<?php

namespace Drupal\lei_core\Controller;

use Drupal\lei_entity\Controller\EntityController;
use Drupal\lei_entity\EntityInterface;

/**
 * Class RestaurantController.
 *
 * Returns responses for Restaurant routes.
 */
class RestaurantController extends EntityController
{
  /**
   * @return int
   */
  protected function getEntityTypeId()
  {
    return 'restaurant';
  }

  public function revisionOverview(EntityInterface $restaurant)
  {
    return parent::revisionOverview($restaurant);
  }

//  public function revisionShow($restaurant_revision)
//  {
//    return parent::revisionShow($restaurant_revision);
//  }

//  public function revisionPageTitle($restaurant_revision)
//  {
//    return parent::revisionPageTitle($restaurant_revision);
//  }
}
