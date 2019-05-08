<?php

namespace Drupal\lei_restaurant;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Foo entity.
 *
 * @see \Drupal\lei_restaurant\Entity\Foo.
 */
class FooAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\lei_restaurant\Entity\FooInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished foo entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published foo entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit foo entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete foo entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add foo entities');
  }

}
