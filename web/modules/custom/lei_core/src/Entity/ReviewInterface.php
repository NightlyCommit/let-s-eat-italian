<?php

namespace Drupal\lei_core\Entity;

use Drupal\lei_core\EntityInterface;
use Drupal\lei_restaurant\Entity\RestaurantInterface;

/**
 * Provides an interface for defining Review entities.
 *
 * @ingroup lei_core
 */
interface ReviewInterface extends EntityInterface
{
  /**
   * @return string
   */
  public function getBody();

  /**
   * @return float
   */
  public function getScore();

  /**
   * @return integer
   */
  public function getRestaurantId();

  /**
   * @return RestaurantInterface
   */
  public function getRestaurant();
}
