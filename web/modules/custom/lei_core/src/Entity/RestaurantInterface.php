<?php

namespace Drupal\lei_core\Entity;

use Drupal\lei_core\EntityInterface;

/**
 * Provides an interface for defining Restaurant entities.
 *
 * @ingroup lei_restaurant
 */
interface RestaurantInterface extends EntityInterface
{
  /**
   * @return string
   */
  public function getName();

  /**
   * @param string $name
   */
  public function setName($name);

  /**
   * @return string
   */
  public function getAddress();

  /**
   * @param string $address
   * @return RestaurantInterface
   */
  public function setAddress($address);

  /**
   * @return float
   */
  public function getRating();
}
