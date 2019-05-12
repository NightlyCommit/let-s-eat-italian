<?php

namespace Drupal\lei_entity\Plugin\Derivative;

use Drupal\lei_entity\Entity\EntityTypeInterface;

class EntityLocalActions extends DeriverBase
{

  /**
   * {@inheritdoc}
   */
  public function getDerivatives(EntityTypeInterface $entityType, array $base_plugin_definition)
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
