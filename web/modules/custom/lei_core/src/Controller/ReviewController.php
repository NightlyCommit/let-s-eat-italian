<?php

namespace Drupal\lei_core\Controller;

use Drupal\lei_core\EntityControllerBase;

/**
 * Class ReviewController.
 *
 * Returns responses for Review routes.
 */
class ReviewController extends EntityControllerBase
{
  /**
   * @return int
   */
  protected function getEntityTypeId()
  {
    return 'review';
  }
}
