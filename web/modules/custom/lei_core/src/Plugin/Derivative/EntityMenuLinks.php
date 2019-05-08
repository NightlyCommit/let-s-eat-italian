<?php

namespace Drupal\lei_core\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityMenuLinks extends DeriverBase implements ContainerDeriverInterface
{
  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * EntityLocalTasks constructor.
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager)
  {
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container, $base_plugin_id)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  protected function getAllowedProviders() {
    return [
      'lei_core'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition)
  {
    $this->derivatives = [];

    $entityTypes = $this->entityTypeManager->getDefinitions();

    foreach ($entityTypes as $entityTypeId => $entityType) {
      if ($entityType instanceof ContentEntityTypeInterface) {
        if (array_search($entityType->getProvider(), $this->getAllowedProviders()) !== false) {
          $key = $entityTypeId . '.admin.structure.settings';

          $this->derivatives[$key] = $base_plugin_definition;
          $this->derivatives[$key]['title'] = $entityType->getPluralLabel();
          $this->derivatives[$key]['base_route'] = ' system.admin_structure';
          $this->derivatives[$key]['route_name'] = $entityTypeId . '.settings';
          $this->derivatives[$key]['description'] = t('Create and manage fields, forms, and display settings for your ' . strtolower($entityType->getPluralLabel()) . '.');
        }
      }
    }

    uasort($this->derivatives, function($a, $b) {
      return ($a['title'] < $b['title']) ? -1 : 1;
    });

    return $this->derivatives;
  }
}
