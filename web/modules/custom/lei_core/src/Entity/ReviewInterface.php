<?php

namespace Drupal\lei_core\Entity;

use Drupal\lei_entity\EntityInterface;

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
