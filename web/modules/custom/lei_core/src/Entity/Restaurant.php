<?php

namespace Drupal\lei_core\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\lei_core\Field\RestaurantRatingItemList;
use Drupal\lei_core\Field\RestaurantReviewsItemList;
use Drupal\lei_entity\EntityBase;

/**
 * Defines the Restaurant entity.
 *
 * @ingroup lei_core
 *
 * @LEIEntityType(
 *   id = "restaurant",
 *   label = @Translation("Restaurant"),
 *   label_plural = @Translation("Restaurants")
 * )
 */
class Restaurant extends EntityBase implements RestaurantInterface
{
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Name'))
      ->setDescription(new TranslatableMarkup('The name of the restaurant.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['address'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Address'))
      ->setDescription(new TranslatableMarkup('The address of the restaurant.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 25,
        'settings' => [
          'rows' => 4,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['rating'] = BaseFieldDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Rating'))
      ->setComputed(TRUE)
      ->setTranslatable(FALSE)
      ->setClass(RestaurantRatingItemList::class)
      ->setDescription(new TranslatableMarkup('The rating of the restaurant.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'default',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['reviews'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Reviews'))
      ->setComputed(TRUE)
      ->setTranslatable(FALSE)
      ->setClass(RestaurantReviewsItemList::class)
      ->setSetting('target_type', 'review')
      ->setDescription(new TranslatableMarkup('The reviews of the restaurant.'))
      ->setDisplayConfigurable('view', TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Type'))
      ->setSetting('target_type', 'restaurant_type')
      ->setDescription(new TranslatableMarkup('The type of the restaurant.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'label'
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

    return $fields;
  }

  /**
   * @return array|string[]
   */
  public function getCacheTagsToInvalidate()
  {
    $reviewsTags = [];

    foreach ($this->getReviews() as $review) {
      $reviewsTags[] = $review->getCacheTags();
    }

    return Cache::mergeTags(
      parent::getCacheTagsToInvalidate(),
      $reviewsTags
    );
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->get('name')->value;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->set('name', $name);
  }

  /**
   * Gets the restaurant address.
   *
   * @return string
   */
  public function getAddress()
  {
    return $this->get('address')->value;
  }

  /**
   * Sets the restaurant address.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->set('address', $address);
  }

  /**
   * @return float
   */
  public function getRating()
  {
    return $this->get('rating')->value;
  }

  /**
   * @return ReviewInterface[]
   */
  public function getReviews()
  {
    $reviews = [];
    $reviewsList = $this->get('reviews');

    foreach ($reviewsList as $reviewItem) {
      /** @type EntityReferenceItem $reviewItem */
      $reviews[] = $reviewItem->entity;
    }

    return $reviews;
  }

  /**
   * @return RestaurantTypeInterface
   */
  public function getType()
  {
    return $this->get('type')->entity;
  }

  /**
   * @param RestaurantTypeInterface $type
   */
  public function setType(RestaurantTypeInterface $type)
  {
    $this->set('type', $type);
  }
}
