<?php

namespace Drupal\lei_core\Entity;

/**
 * Provides Views data for entities.
 */
class EntityViewsData extends \Drupal\views\EntityViewsData {

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
