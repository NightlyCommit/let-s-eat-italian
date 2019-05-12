<?php

namespace Drupal\lei_core\Entity;

use Drupal\lei_entity\EntityInterface;

/**
 * Provides an interface for defining Restaurant type entities.
 *
 * @ingroup lei_core
 */
interface RestaurantTypeInterface extends EntityInterface
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
  public function getDescription();

  /**
   * @param string $description
   */
  public function setDescription($description);

  /**
   * @return RestaurantInterface[]
   */
  public function getRestaurants();
}
