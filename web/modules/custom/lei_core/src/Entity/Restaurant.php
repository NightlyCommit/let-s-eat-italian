<?php

namespace Drupal\lei_core\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\lei_core\EntityBase;
use Drupal\lei_restaurant\RatingItemList;

/**
 * Defines the Restaurant entity.
 *
 * @ingroup lei_core
 *
 * @ContentEntityType(
 *   id = "restaurant",
 *   label = @Translation("Restaurant"),
 *   label_plural = @Translation("Restaurants"),
 *   handlers = {
 *     "storage" = "Drupal\lei_core\RestaurantStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lei_core\RestaurantListBuilder",
 *     "views_data" = "Drupal\lei_core\Entity\RestaurantViewsData",
 *     "translation" = "Drupal\lei_core\RestaurantTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\lei_core\Form\RestaurantForm",
 *       "add" = "Drupal\lei_core\Form\RestaurantForm",
 *       "edit" = "Drupal\lei_core\Form\RestaurantForm",
 *       "delete" = "Drupal\lei_core\Form\RestaurantDeleteForm",
 *     },
 *     "access" = "Drupal\lei_core\RestaurantAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\lei_core\RestaurantHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "restaurant",
 *   data_table = "restaurant_field_data",
 *   revision_table = "restaurant_revision",
 *   revision_data_table = "restaurant_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer restaurant entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/restaurant/{restaurant}",
 *     "add-form" = "/restaurant/add",
 *     "edit-form" = "/restaurant/{restaurant}/edit",
 *     "delete-form" = "/restaurant/{restaurant}/delete",
 *     "version-history" = "/restaurant/{restaurant}/revisions",
 *     "revision" = "/restaurant/{restaurant}/revisions/{revision}/view",
 *     "revision_revert" = "/admin/content/restaurant/{restaurant}/revisions/{revision}/revert",
 *     "revision_delete" = "/admin/content/restaurant/{restaurant}/revisions/{revision}/delete",
 *     "translation_revert" = "/admin/content/restaurant/{restaurant}/revisions/{revision}/revert/{langcode}",
 *     "collection" = "/admin/content/restaurant",
 *   },
 *   field_ui_base_route = "restaurant.settings"
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
      ->setDescription(t('The name of the restaurant entity.'))
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
