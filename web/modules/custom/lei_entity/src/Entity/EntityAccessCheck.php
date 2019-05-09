<?php

namespace Drupal\lei_entity\Entity;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Access\AccessResultNeutral;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\lei_entity\EntityInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides a custom access checker for entities.
 */
class EntityAccessCheck extends \Drupal\Core\Entity\EntityAccessCheck
{
  /**
   * @param Route $route
   * @param RouteMatchInterface $route_match
   * @param AccountInterface $account
   * @return bool|AccessResultInterface|AccessResultNeutral
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account)
  {
    /** @var AccessResultInterface $result */
    $result = NULL;

    // Split the entity type and the operation.
    $requirement = $route->getRequirement('_entity_access');
    list($entity_type, $operation) = explode('.', $requirement);
    // If $entity_type parameter is a valid entity, call its own access check.
    $parameters = $route_match->getParameters();

    if ($parameters->has('entity')) {
      $entity = $parameters->get('entity');

      if ($entity instanceof EntityInterface) {
        $result = $entity->access($operation, $account, TRUE);
      }
    }

    if ($result === NULL) {
      $result = parent::access($route, $route_match, $account);
    }

    return $result;
  }
}
