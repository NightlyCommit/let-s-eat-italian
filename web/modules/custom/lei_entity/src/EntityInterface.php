<?php

namespace Drupal\lei_entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;

interface EntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface
{
  /**
   * Denotes that the entity is not published.
   */
  const NOT_PUBLISHED = 0;

  /**
   * Denotes that the entity is published.
   */
  const PUBLISHED = 1;

  /**
   * @return int
   */
  public function getCreatedTime();

  /**
   * @param int $timestamp
   */
  public function setCreatedTime($timestamp);

  /**
   * @return bool
   */
  public function isPublished();

  /**
   * @param bool $published
   */
  public function setPublished($published);
}
