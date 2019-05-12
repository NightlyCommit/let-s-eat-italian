<?php

namespace Drupal\lei_entity;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Views data for entities.
 */
class EntityViewsData extends \Drupal\views\EntityViewsData
{

  /**
   * @var EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  public function __construct(EntityTypeInterface $entity_type, SqlEntityStorageInterface $storage_controller, EntityManagerInterface $entity_manager, ModuleHandlerInterface $module_handler, TranslationInterface $translation_manager, EntityFieldManagerInterface $entity_field_manager)
  {
    parent::__construct($entity_type, $storage_controller, $entity_manager, $module_handler, $translation_manager);

    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type)
  {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('entity.manager'),
      $container->get('module_handler'),
      $container->get('string_translation'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getViewsData()
  {
    $data = parent::getViewsData();

    $definitions = $this->entityFieldManager->getBaseFieldDefinitions($this->entityType->id());

    foreach ($definitions as $key => $definition) {
      if ($definition->isComputed()) {
        $data[$this->entityType->getBaseTable()][$definition->getName()] = [
          'title' => $definition->getLabel(),
          'help' => $definition->getDescription(),
          'field' => [
            'id' => 'field',
            'field_name' => $definition->getName(), // @see https://www.drupal.org/node/2904410
          ],
        ];
      }
    }

    return $data;
  }
}
