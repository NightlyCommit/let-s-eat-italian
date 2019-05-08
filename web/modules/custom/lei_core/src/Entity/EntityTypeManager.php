<?php


namespace Drupal\lei_core\Entity;


use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Entity\EntityLastInstalledSchemaRepositoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Class EntityTypeManager
 *
 * @package Drupal\lei_core\Entity
 * @see https://www.drupal.org/project/drupal/issues/3053490
 */
class EntityTypeManager extends \Drupal\Core\Entity\EntityTypeManager
{
  public function __construct(\Traversable $namespaces, ModuleHandlerInterface $module_handler, CacheBackendInterface $cache, TranslationInterface $string_translation, ClassResolverInterface $class_resolver, EntityLastInstalledSchemaRepositoryInterface $entity_last_installed_schema_repository, array $additional_annotation_namespaces = [])
  {
    parent::__construct($namespaces, $module_handler, $cache, $string_translation, $class_resolver, $entity_last_installed_schema_repository, $additional_annotation_namespaces);

    $this->discovery = new AnnotatedClassDiscovery('Entity', $namespaces, 'Drupal\Core\Entity\Annotation\EntityType', [
      'Drupal\lei_core\Entity\Annotation'
    ]);
  }
}
