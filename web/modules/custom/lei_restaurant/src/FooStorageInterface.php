<?php

namespace Drupal\lei_restaurant;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\lei_restaurant\Entity\FooInterface;

/**
 * Defines the storage handler class for Foo entities.
 *
 * This extends the base storage class, adding required special handling for
 * Foo entities.
 *
 * @ingroup lei_restaurant
 */
interface FooStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Foo revision IDs for a specific Foo.
   *
   * @param \Drupal\lei_restaurant\Entity\FooInterface $entity
   *   The Foo entity.
   *
   * @return int[]
   *   Foo revision IDs (in ascending order).
   */
  public function revisionIds(FooInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Foo author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Foo revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\lei_restaurant\Entity\FooInterface $entity
   *   The Foo entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(FooInterface $entity);

  /**
   * Unsets the language for all Foo with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
