<?php

namespace Drupal\lei_entity;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Defines the storage handler class for entities.
 *
 * @ingroup lei_entity
 */
class EntityStorage extends SqlContentEntityStorage implements EntityStorageInterface
{

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityInterface $entity)
  {
    return $this->database->query(
      'SELECT vid FROM {' . $this->getEntityType()->getRevisionTable() . '} WHERE id=:id ORDER BY vid',
      [
        ':id' => $entity->id()
      ]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account)
  {
    return $this->database->query(
      'SELECT vid FROM {' . $this->getEntityType()->getRevisionDataTable() . '} WHERE uid = :uid ORDER BY vid',
      [
        ':uid' => $account->id()
      ]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityInterface $entity)
  {
    return $this->database->query('SELECT COUNT(*) FROM {' . $this->getEntityType()->getRevisionTable() . '} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language)
  {
    return $this->database->update($this->getEntityType()->getRevisionTable())
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }
}
