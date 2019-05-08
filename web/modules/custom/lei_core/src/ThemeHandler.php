<?php
/**
 * Created by PhpStorm.
 * User: ericmorand
 * Date: 22.02.19
 * Time: 20:42
 */

namespace Drupal\lei_core;

use Drupal\Core\Extension\Exception\UninstalledExtensionException;

class ThemeHandler extends \Drupal\Core\Extension\ThemeHandler
{

  /**
   * {@inheritdoc}
   */
  public function setAdmin($name)
  {
    $list = $this->listInfo();

    if (!isset($list[$name])) {
      throw new UninstalledExtensionException("$name theme is not installed.");
    }

    $this->configFactory->getEditable('system.theme')
      ->set('admin', $name)
      ->save();

    return $this;
  }
}
