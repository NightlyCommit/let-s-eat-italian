<?php

namespace Drupal\lei_core\Controller;

use Drupal\lei_entity\Controller\EntityController;
use Drupal\lei_entity\EntityInterface;

/**
 * Class ReviewController.
 *
 * Returns responses for Review routes.
 */
class ReviewController extends EntityController
{
  /**
   * @return int
   */
  protected function getEntityTypeId()
  {
    return 'review';
  }

  public function revisionOverview(EntityInterface $review)
  {
    return parent::revisionOverview($review);
  }

  public function revisionShow($review_revision)
  {
    return parent::revisionShow($review_revision);
  }
}
