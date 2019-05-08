<?php

namespace Drupal\lei_core\Controller;

use Drupal\lei_core\EntityControllerBase;

/**
 * Class RestaurantController.
 *
 * Returns responses for Restaurant routes.
 */
class RestaurantController extends EntityControllerBase
{
  /**
   * @return int
   */
  protected function getEntityTypeId()
  {
    return 'restaurant';
  }

  public function revisionOverview(\Drupal\lei_core\EntityInterface $restaurant)
  {
    return parent::revisionOverview($restaurant);
  }
}
