<?php

namespace Drupal\lei_entity\Plugin\Derivative;

use Drupal\lei_entity\Entity\LEIEntityTypeInterface;

class EntityCollectionTab extends DeriverBase
{

  /**
   * {@inheritdoc}
   */
  public function getDerivatives(LEIEntityTypeInterface $entityType, array $base_plugin_definition)
  {
    $derivative = [];

    $key = 'entity.' . $entityType->id() . '.collection_tab';

    $derivative[$key] = $base_plugin_definition;
    $derivative[$key]['title'] = $entityType->getPluralLabel();
    $derivative[$key]['route_name'] = 'entity.' . $entityType->id() . '.collection';
    $derivative[$key]['base_route'] = 'system.admin_content';

    return $derivative;
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
