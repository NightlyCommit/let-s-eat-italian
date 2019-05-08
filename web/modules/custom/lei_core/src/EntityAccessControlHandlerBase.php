<?php

namespace Drupal\lei_core;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the entity.
 *
 * @see \Drupal\lei_core\EntityInterface.
 */
abstract class EntityAccessControlHandlerBase extends EntityAccessControlHandler
{

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account)
  {
    /** @var \Drupal\lei_core\EntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished ' . $this->entityTypeId . ' entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published ' . $this->entityTypeId . ' entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit ' . $this->entityTypeId . ' entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete ' . $this->entityTypeId . ' entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL)
  {
    return AccessResult::allowedIfHasPermission($account, 'add ' . $this->entityTypeId . ' entities');
  }

}
