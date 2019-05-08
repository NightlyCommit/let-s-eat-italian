<?php

namespace Drupal\lei_restaurant;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Restaurant entity.
 *
 * @see \Drupal\lei_restaurant\Entity\Restaurant.
 */
class RestaurantAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\lei_restaurant\Entity\RestaurantInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished restaurant entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published restaurant entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit restaurant entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete restaurant entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add restaurant entities');
  }

}
