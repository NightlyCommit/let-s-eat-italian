<?php

namespace Drupal\lei_restaurant\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Foo entities.
 */
class FooViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
