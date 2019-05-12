<?php

namespace Drupal\lei_entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for entities.
 *
 * @see \Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see \Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class EntityHtmlRouteProvider extends AdminHtmlRouteProvider
{

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type)
  {
    /** @var EntityTypeInterface $entity_type */
    $collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();

    if ($history_route = $this->getHistoryRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.version_history", $history_route);
    }

    if ($revision_route = $this->getRevisionRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision", $revision_route);
    }

    if ($revert_route = $this->getRevisionRevertRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision_revert", $revert_route);
    }

    if ($delete_route = $this->getRevisionDeleteRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision_delete", $delete_route);
    }

    if ($translation_route = $this->getRevisionTranslationRevertRoute($entity_type)) {
      $collection->add("{$entity_type_id}.revision_revert_translation_confirm", $translation_route);
    }

    if ($settings_form_route = $this->getSettingsFormRoute($entity_type)) {
      $collection->add("{$entity_type_id}.settings", $settings_form_route);
    }

    return $collection;
  }

  /**
   * Gets the version history route.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return Route|null
   *   The generated route, if available.
   */
  protected function getHistoryRoute(EntityTypeInterface $entity_type)
  {
    /** @var Route $route */
    $route = NULL;

    if ($entity_type->hasLinkTemplate('version-history')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('version-history'));

      $route
        ->setDefaults([
          '_title' => "{$entity_type->getLabel()} revisions",
          '_controller' => '\Drupal\lei_entity\Controller\EntityController::revisionOverview',
          'entity_type_id' => $entity_type_id
        ])
        ->setRequirement('_permission', 'access ' . $entity_type_id . ' revisions')
        ->setOption('_admin_route', TRUE)
        ->setOption('parameters', [
          $entity_type_id => [
            'type' => 'entity:' . $entity_type_id
          ],
        ]);
    }

    return $route;
  }

  /**
   * Gets the revision route.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return Route|null
   *   The generated route, if available.
   */
  protected function getRevisionRoute(EntityTypeInterface $entity_type)
  {
    /** @var Route $route */
    $route = NULL;

    if ($entity_type->hasLinkTemplate('revision')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('revision'));

      $route
        ->setDefaults([
          '_controller' => '\Drupal\lei_entity\Controller\EntityController::revisionShow',
          '_title_callback' => '\Drupal\lei_entity\Controller\EntityController::revisionPageTitle',
          'entity_type_id' => $entity_type_id
        ])
        ->setRequirement('_permission', 'access ' . $entity_type_id . ' revisions')
        ->setOption('_admin_route', TRUE)
        ->setOption('parameters', [
          $entity_type_id => [
            'type' => 'entity:' . $entity_type_id
          ],
        ]);
    }

    return $route;
  }

  /**
   * Gets the revision revert route.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return Route|null
   *   The generated route, if available.
   */
  protected function getRevisionRevertRoute(EntityTypeInterface $entity_type)
  {
    /** @var Route $route */
    $route = NULL;

    if ($entity_type->hasLinkTemplate('revision_revert')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('revision_revert'));

      $route
        ->setDefaults([
          '_form' => '\Drupal\lei_entity\Form\EntityRevisionRevertForm',
          '_title' => 'Revert to earlier revision',
          'entity_type_id' => $entity_type_id
        ])
        ->setRequirement('_permission', 'revert all ' . $entity_type_id . ' revisions')
        ->setOption('_admin_route', TRUE)
        ->setOption('parameters', [
          $entity_type_id => [
            'type' => 'entity:' . $entity_type_id
          ],
        ]);
    }

    return $route;
  }

  /**
   * Gets the revision delete route.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return Route|null
   *   The generated route, if available.
   */
  protected function getRevisionDeleteRoute(EntityTypeInterface $entity_type)
  {
    /** @var Route $route */
    $route = NULL;

    if ($entity_type->hasLinkTemplate('revision_delete')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('revision_delete'));

      $route
        ->setDefaults([
          '_form' => '\Drupal\lei_entity\Form\EntityRevisionDeleteForm',
          '_title' => 'Delete earlier revision',
          'entity_type_id' => $entity_type_id
        ])
        ->setRequirement('_permission', 'delete all ' . $entity_type_id . ' revisions')
        ->setOption('_admin_route', TRUE)
        ->setOption('parameters', [
          $entity_type_id => [
            'type' => 'entity:' . $entity_type_id
          ],
        ]);
    }

    return $route;
  }

  /**
   * Gets the revision translation revert route.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return Route|null
   *   The generated route, if available.
   */
  protected function getRevisionTranslationRevertRoute(EntityTypeInterface $entity_type)
  {
    /** @var Route $route */
    $route = NULL;

    if ($entity_type->hasLinkTemplate('translation_revert')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('translation_revert'));

      $route
        ->setDefaults([
          '_form' => '\Drupal\lei_entity\Form\ReviewRevisionRevertTranslationForm',
          '_title' => 'Revert to earlier revision of a translation',
          'entity_type_id' => $entity_type_id
        ])
        ->setRequirement('_permission', 'revert all ' . $entity_type_id . ' revisions')
        ->setOption('_admin_route', TRUE)
        ->setOption('parameters', [
          $entity_type_id => [
            'type' => 'entity:' . $entity_type_id
          ],
        ]);
    }

    return $route;
  }

  /**
   * Gets the settings form route.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return Route|null
   *   The generated route, if available.
   */
  protected function getSettingsFormRoute(EntityTypeInterface $entity_type)
  {
    $entity_type_id = $entity_type->id();
    $route = new Route("/admin/structure/{$entity_type_id}/settings");

    $route
      ->setDefaults([
        '_form' => '\Drupal\lei_entity\Form\EntityTypeForm',
        '_title' => "{$entity_type->getLabel()} settings",
        'entity_type_id' => $entity_type_id
      ])
      ->setRequirement('_permission', $entity_type->getAdminPermission())
      ->setOption('_admin_route', TRUE)
      ->setOption('parameters', [
        $entity_type_id => [
          'type' => 'entity:' . $entity_type_id
        ],
      ]);

    return $route;
  }
}
