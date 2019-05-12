<?php

namespace Drupal\lei_entity\Plugin\Derivative;

use Drupal\lei_entity\Entity\EntityTypeInterface;

class EntityLocalTasks extends DeriverBase
{
  /**
   * {@inheritdoc}
   */
  public function getDerivatives(EntityTypeInterface $entityType, array $base_plugin_definition)
  {
    $derivatives = [];
    $entityTypeId = $entityType->id();

    // canonical
    $key = 'entity.' . $entityTypeId . '.canonical';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = t('View');
    $derivatives[$key]['route_name'] = 'entity.' . $entityTypeId . '.canonical';
    $derivatives[$key]['base_route'] = 'entity.' . $entityTypeId . '.canonical';

    // edit form
    $key = 'entity.' . $entityTypeId . '.edit_form';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = t('Edit');
    $derivatives[$key]['route_name'] = 'entity.' . $entityTypeId . '.edit_form';
    $derivatives[$key]['base_route'] = 'entity.' . $entityTypeId . '.canonical';

    // revisions tab
    $key = 'entity.' . $entityTypeId . '.revisions_tab';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = t('Revisions');
    $derivatives[$key]['base_route'] = 'entity.' . $entityTypeId . '.canonical';
    $derivatives[$key]['route_name'] = 'entity.' . $entityTypeId . '.version_history';

    // delete tab
    $key = 'entity.' . $entityTypeId . '.delete_form';

    $derivatives[$key] = $base_plugin_definition;
    $derivatives[$key]['title'] = t('Delete');
    $derivatives[$key]['base_route'] = 'entity.' . $entityTypeId . '.canonical';
    $derivatives[$key]['route_name'] = 'entity.' . $entityTypeId . '.delete_form';

    return $derivatives;
  }
}
