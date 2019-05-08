<?php

namespace Drupal\lei_core\Plugin\Derivative;

use Drupal\lei_core\Entity\LEIEntityTypeInterface;

class EntityLocalActions extends DeriverBase
{

  /**
   * {@inheritdoc}
   */
  public function getDerivatives(LEIEntityTypeInterface $entityType, array $base_plugin_definition)
  {
    $derivatives = [];

    $key = 'entity.' . $entityType->id() . '.add_form';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = 'Add a ' . strtolower($entityType->getLabel());
    $derivatives[$key]['route_name'] = 'entity.' . $entityType->id() . '.add_form';
    $derivatives[$key]['appears_on'] = [
      'entity.' . $entityType->id() . '.collection'
    ];

    return $derivatives;
  }
}
