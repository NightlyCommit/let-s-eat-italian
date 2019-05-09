<?php

namespace Drupal\lei_core\Form;

/**
 * Provides a form for reverting a Review revision.
 *
 * @ingroup lei_core
 */
class ReviewRevisionRevertForm extends EntityRevisionRevertFormBase {

  /**
   * @return string
   */
  static function entityTypeId()
  {
    return 'review';
  }
}
