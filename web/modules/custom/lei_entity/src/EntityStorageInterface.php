<?php

namespace Drupal\lei_entity;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Defines the storage handler class for entities.
 *
 * This extends the base storage class, adding required special handling for entities.
 *
 * @ingroup lei_entity
 */
interface EntityStorageInterface extends ContentEntityStorageInterface
{

  /**
   * Gets a list of revision IDs for a specific entity.
   *
   * @param EntityInterface $entity The entity.
   *
   * @return int[] Revision IDs (in ascending order).
   */
  public function revisionIds(EntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as entity author.
   *
   * @param AccountInterface $account The user entity.
   *
   * @return int[] Revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param EntityInterface $entity The entity.
   *
   * @return int The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityInterface $entity);

  /**
   * Unsets the language for all entities with the given language.
   *
   * @param LanguageInterface $language The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
