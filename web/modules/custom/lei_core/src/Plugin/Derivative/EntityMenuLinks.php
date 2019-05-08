<?php

namespace Drupal\lei_core\Plugin\Derivative;

use Drupal\lei_core\Entity\LEIEntityTypeInterface;

class EntityMenuLinks extends DeriverBase
{

  /**
   * {@inheritdoc}
   */
  public function getDerivatives(LEIEntityTypeInterface $entityType, array $base_plugin_definition)
  {
    $derivatives = [];

    $key = $entityType->id() . '.admin.structure.settings';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = $entityType->getPluralLabel();
    $derivatives[$key]['parent'] = 'system.admin_structure';
    $derivatives[$key]['route_name'] = $entityType->id() . '.settings';
    $derivatives[$key]['description'] = t('Create and manage fields, forms, and display settings for your @label.', [
      '@label' => strtolower($entityType->getPluralLabel())
    ]);

    return $derivatives;
  }

  public function getDerivativeDefinitions($base_plugin_definition)
  {
    $definitions = parent::getDerivativeDefinitions($base_plugin_definition);

    uasort($definitions, function ($a, $b) {
      return ($a['title'] < $b['title']) ? -1 : 1;
    });

    return $definitions;
  }
}
