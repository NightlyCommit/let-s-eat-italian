<?php

namespace Drupal\lei_core\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
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

    $fields['restaurant'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Restaurant'))
      ->setSetting('target_type', 'restaurant')
      ->setDescription(new TranslatableMarkup('The restaurant the review is about.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_entity_view',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['score'] = BaseFieldDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Score'))
      ->setDescription(t('The score of the review.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(NULL)
      ->addPropertyConstraints('value', [
        'Range' => [
          'min' => 0,
          'max' => 5
        ]
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

  public function label()
  {
    return new TranslatableMarkup('A review by %author', [
      '%author' => $this->getOwner()->label()
    ]);
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
    return $this->get('score')->value;
  }

  /**
   * @return integer
   */
  public function getRestaurantId()
  {
    return $this->get('restaurant')->target_id;
  }

  /**
   * @return RestaurantInterface
   */
  public function getRestaurant()
  {
    return $this->get('restaurant')->entity;
  }
}
