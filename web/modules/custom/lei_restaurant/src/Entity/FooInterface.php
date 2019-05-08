<?php

namespace Drupal\lei_restaurant\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Foo entities.
 *
 * @ingroup lei_restaurant
 */
interface FooInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Foo name.
   *
   * @return string
   *   Name of the Foo.
   */
  public function getName();

  /**
   * Sets the Foo name.
   *
   * @param string $name
   *   The Foo name.
   *
   * @return \Drupal\lei_restaurant\Entity\FooInterface
   *   The called Foo entity.
   */
  public function setName($name);

  /**
   * Gets the Foo creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Foo.
   */
  public function getCreatedTime();

  /**
   * Sets the Foo creation timestamp.
   *
   * @param int $timestamp
   *   The Foo creation timestamp.
   *
   * @return \Drupal\lei_restaurant\Entity\FooInterface
   *   The called Foo entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Foo published status indicator.
   *
   * Unpublished Foo are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Foo is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Foo.
   *
   * @param bool $published
   *   TRUE to set this Foo to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\lei_restaurant\Entity\FooInterface
   *   The called Foo entity.
   */
  public function setPublished($published);

  /**
   * Gets the Foo revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Foo revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\lei_restaurant\Entity\FooInterface
   *   The called Foo entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Foo revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Foo revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\lei_restaurant\Entity\FooInterface
   *   The called Foo entity.
   */
  public function setRevisionUserId($uid);

}
