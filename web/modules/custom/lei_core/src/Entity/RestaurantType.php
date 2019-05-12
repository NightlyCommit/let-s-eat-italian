<?php

namespace Drupal\lei_core\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\lei_core\Field\RestaurantTypeRestaurantsItemList;
use Drupal\lei_entity\EntityBase;

/**
 * Defines the Restaurant type entity.
 *
 * @ingroup lei_core
 *
 * @LEIEntityType(
 *   id = "restaurant_type",
 *   label = @Translation("Restaurant type"),
 *   label_plural = @Translation("Restaurant types")
 * )
 */
class RestaurantType extends EntityBase implements RestaurantTypeInterface
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

    $fields['description'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDescription(new TranslatableMarkup('The description of the restaurant.'))
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
        'type' => 'string_textarea',
        'weight' => 25,
        'settings' => [
          'rows' => 4,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['restaurants'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Restaurants'))
      ->setComputed(TRUE)
      ->setTranslatable(FALSE)
      ->setClass(RestaurantTypeRestaurantsItemList::class)
      ->setSetting('target_type', 'restaurant')
      ->setDescription(new TranslatableMarkup('The restaurants of that type.'))
      ->setDisplayConfigurable('view', TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    return $fields;
  }

  /**
   * @return array|string[]
   */
  public function getCacheTagsToInvalidate()
  {
    $restaurantsTags = [];

    foreach ($this->getRestaurants() as $restaurant) {
      $restaurantsTags[] = $restaurant->getCacheTags();
    }

    return Cache::mergeTags(
      parent::getCacheTagsToInvalidate(),
      $restaurantsTags
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
   * @return string
   */
  public function getDescription()
  {
    return $this->get('description')->value;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->set('description', $description);
  }

  /**
   * @return RestaurantInterface[]
   */
  public function getRestaurants()
  {
    $restaurants = [];
    $restaurant_list = $this->get('restaurants');

    foreach ($restaurant_list as $restaurant_item) {
      /** @type EntityReferenceItem $restaurant_item */
      $restaurants[] = $restaurant_item->entity;
    }

    return $restaurants;
  }
}
