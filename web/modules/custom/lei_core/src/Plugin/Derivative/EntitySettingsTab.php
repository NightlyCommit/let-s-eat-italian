<?php

namespace Drupal\lei_core\Plugin\Derivative;

use Drupal\lei_core\Entity\LEIEntityTypeInterface;

class EntitySettingsTab extends DeriverBase
{

  /**
   * {@inheritdoc}
   */
  public function getDerivatives(LEIEntityTypeInterface $entityType, array $base_plugin_definition)
  {
    $derivatives = [];

    $key = 'entity.' . $entityType->id() . '.settings_tab';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = t('Settings');
    $derivatives[$key]['route_name'] = $entityType->id() . '.settings';
    $derivatives[$key]['base_route'] = $entityType->id() . '.settings';

    return $derivatives;
  }
}
