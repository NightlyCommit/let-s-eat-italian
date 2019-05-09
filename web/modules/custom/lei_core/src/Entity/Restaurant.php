<?php

namespace Drupal\lei_core\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\lei_core\Field\RatingItemList;
use Drupal\lei_entity\EntityBase;

/**
 * Defines the Restaurant entity.
 *
 * @ingroup lei_core
 *
 * @LEIEntityType(
 *   id = "restaurant",
 *   label = @Translation("Restaurant"),
 *   label_plural = @Translation("Restaurants"),
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
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the restaurant.'))
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
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['address'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Address'))
      ->setDescription(t('The address of the restaurant.'))
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
      ->setLabel(t('Rating'))
      ->setReadOnly(TRUE)
      ->setComputed(TRUE)
      ->setClass(RatingItemList::class)
      ->setDescription(t('The rating of the restaurant.'))
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
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
   * @return RestaurantInterface
   */
  public function setAddress($address)
  {
    $this->set('address', $address);
    return $this;
  }

  /**
   * @return float
   */
  public function getRating()
  {
    return $this->get('rating')->value;
  }
}
