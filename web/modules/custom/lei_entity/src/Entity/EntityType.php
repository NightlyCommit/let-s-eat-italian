<?php

namespace Drupal\lei_entity\Entity;

use Drupal\Core\Entity\ContentEntityType;

class EntityType extends ContentEntityType implements EntityTypeInterface
{
  /** @var array */
  protected $routeNames;

  public function __construct($definition)
  {
    $id = $definition['id'];

    $defaultDefinition['links'] = [
      'canonical' => "/{$id}/{{$id}}",
      'add-form' => "/{$id}/add",
      'edit-form' => "/{$id}/{{$id}}/edit",
      'delete-form' => "/{$id}/{{$id}}/delete",
      'version-history' => "/{$id}/{{$id}}/revisions",
      'revision' => "/{$id}/{{$id}}/revision/{{$id}_revision}",
      'revision_revert' => "/admin/content/{$id}/{{$id}}/revision/{{$id}_revision}/revert",
      'revision_delete' => "/admin/content/{$id}/{{$id}}/revision/{{$id}_revision}/delete",
      'translation_revert' => "/admin/content/{$id}/{{$id}}/revision/{{$id}_revision}/revert/{langcode}",
      'collection' => "/admin/content/{$id}",
    ];

    $defaultDefinition['field_ui_base_route'] = "{$id}.settings";

    $defaultDefinition['handlers'] = [
      'storage' => "Drupal\lei_entity\EntityStorage",
      'view_builder' => "Drupal\Core\Entity\EntityViewBuilder",
      'list_builder' => "Drupal\lei_entity\EntityListBuilder",
      'views_data' => "Drupal\lei_entity\EntityViewsData",
      'translation' => "Drupal\lei_entity\EntityTranslationHandler",
      'form' => [
        'default' => "Drupal\lei_entity\Form\EntityForm",
        'add' => "Drupal\lei_entity\Form\EntityForm",
        'edit' => "Drupal\lei_entity\Form\EntityForm",
        'delete' => "Drupal\lei_entity\Form\EntityDeleteForm",
      ],
      'access' => "Drupal\lei_entity\EntityAccessControlHandler",
      'route_provider' => [
        'html' => "Drupal\lei_entity\EntityHtmlRouteProvider",
      ],
    ];

    $defaultDefinition['base_table'] = $id;
    $defaultDefinition['data_table'] = "{$id}_field_data";
    $defaultDefinition['revision_table'] = "{$id}_revision";
    $defaultDefinition['revision_data_table'] = "{$id}_field_revision";
    $defaultDefinition['show_revision_ui'] = TRUE;
    $defaultDefinition['translatable'] = TRUE;
    $defaultDefinition['admin_permission'] = "administer {$id} entities";

    $defaultDefinition['entity_keys'] = [
      'id' => 'id',
      'revision' => 'vid',
      'label' => 'name',
      'uuid' => 'uuid',
      'owner' => 'uid',
      'langcode' => 'langcode',
      'status' => 'status',
      'published' => 'status'
    ];

    $this->routeNames = [
      'canonical' => "entity.{$id}.canonical"
    ];

    parent::__construct(array_replace_recursive($defaultDefinition, $definition));
  }
}
