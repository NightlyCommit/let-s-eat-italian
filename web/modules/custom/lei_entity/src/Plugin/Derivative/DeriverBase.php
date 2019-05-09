<?php


namespace Drupal\lei_entity\Plugin\Derivative;


use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\lei_entity\Entity\LEIEntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DeriverBase extends \Drupal\Component\Plugin\Derivative\DeriverBase  implements ContainerDeriverInterface
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

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition)
  {
    $this->derivatives = [];

    $entityTypes = $this->entityTypeManager->getDefinitions();

    foreach ($entityTypes as $entityTypeId => $entityType) {
      if ($entityType instanceof LEIEntityTypeInterface) {
        $this->derivatives += $this->getDerivatives($entityType, $base_plugin_definition);
      }
    }

    return $this->derivatives;
  }

  abstract protected function getDerivatives(LEIEntityTypeInterface $entityType, array $base_plugin_definition);
}
