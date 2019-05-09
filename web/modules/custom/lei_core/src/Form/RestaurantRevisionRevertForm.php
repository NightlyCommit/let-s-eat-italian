<?php

namespace Drupal\lei_core\Form;

/**
 * Provides a form for reverting a Restaurant revision.
 *
 * @ingroup lei_core
 */
class RestaurantRevisionRevertForm extends EntityRevisionRevertFormBase {

  /**
   * @return string
   */
  static function entityTypeId()
  {
    return 'restaurant';
  }
}
