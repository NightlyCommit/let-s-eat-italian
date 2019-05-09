<?php


namespace Drupal\lei_core\Entity;


use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lei_entity\EntityBase;

/**
 * Defines the Review entity.
 *
 * @ingroup lei_core
 *
 * @LEIEntityType(
 *   id = "review",
 *   label = @Translation("Review"),
 *   label_plural = @Translation("Reviews"),
 * )
 */
class Review extends EntityBase implements ReviewInterface
{
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['score'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Score'))
      ->setDescription(t('The score of the review.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(NULL)
      ->setDisplayOptions('view', [
        'label' => 'above',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

  /**
   * @return string
   */
  public function getBody()
  {
    // TODO: Implement getBody() method.
  }

  /**
   * @return float
   */
  public function getScore()
  {
    // TODO: Implement getScore() method.
  }

  /**
   * @return integer
   */
  public function getRestaurantId()
  {
    // TODO: Implement getRestaurantId() method.
  }

  /**
   * @return RestaurantInterface
   */
  public function getRestaurant()
  {
    // TODO: Implement getRestaurant() method.
  }
}
