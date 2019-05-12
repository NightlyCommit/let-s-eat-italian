<?php

namespace Drupal\lei_entity\EventSubscriber;

use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\lei_entity\Entity\EntityTypeInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Sets the _admin_route for specific entity routes.
 */
class AdminRouteSubscriber extends RouteSubscriberBase
{

  /**
   * The config factory.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The router builder.
   *
   * @var RouteBuilderInterface
   */
  protected $routerBuilder;

  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new NodeAdminRouteSubscriber.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param RouteBuilderInterface $router_builder
   *   The router builder service.
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteBuilderInterface $router_builder, EntityTypeManagerInterface $entity_type_manager)
  {
    $this->configFactory = $config_factory;
    $this->routerBuilder = $router_builder;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * @return EntityTypeInterface[]
   */
  protected function getEntityTypes()
  {
    $entity_types = [];

    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
      if ($entity_type instanceof EntityTypeInterface) {
        $entity_types[] = $entity_type;
      }
    }

    return $entity_types;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection)
  {
    foreach ($this->getEntityTypes() as $entity_type) {
      $entity_type_id = $entity_type->id();

      if ($this->configFactory->get($entity_type_id . '.settings')->get('use_admin_theme')) {
        foreach ($collection->all() as $route) {
          if ($route->hasOption('_' . $entity_type_id . '_operation_route')) {
            $route->setOption('_admin_route', TRUE);
          }
        }
      }
    }
  }

  /**
   * Rebuilds the router when lei_entity.{entity_type_id}.settings:use_admin_theme is changed.
   *
   * @param ConfigCrudEvent $event
   */
  public function onConfigSave(ConfigCrudEvent $event)
  {
    foreach ($this->getEntityTypes() as $entity_type) {
      $entity_type_id = $entity_type->id();

      if ($event->getConfig()->getName() === $entity_type_id . '.settings' && $event->isChanged('use_admin_theme')) {
        $this->routerBuilder->setRebuildNeeded();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events = parent::getSubscribedEvents();
    $events[ConfigEvents::SAVE][] = ['onConfigSave', 0];

    return $events;
  }
}
