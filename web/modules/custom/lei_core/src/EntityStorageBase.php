<?php

namespace Drupal\lei_core;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\lei_core\Entity\ReviewInterface;
use Drupal\lei_core\ReviewStorageInterface;

/**
 * Defines the storage handler class for Review entities.
 *
 * This extends the base storage class, adding required special handling for
 * Review entities.
 *
 * @ingroup lei_review
 */
abstract class EntityStorageBase extends SqlContentEntityStorage implements EntityStorageInterface
{

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityInterface $entity)
  {
    dump($this->tableMapping); exit;

    return $this->database->query(
      'SELECT vid FROM {review_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account)
  {
    return $this->database->query(
      'SELECT vid FROM {review_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityInterface $entity)
  {
    return $this->database->query('SELECT COUNT(*) FROM {' . $entity->getEntityType()->getRevisionTable() . '} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language)
  {
    return $this->database->update('review_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
