<?php


namespace Drupal\lei_core\Entity;


use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lei_core\EntityBase;

/**
 * Defines the Review entity.
 *
 * @ingroup lei_core
 *
 * @ContentEntityType(
 *   id = "review",
 *   label = @Translation("Review"),
 *   label_plural = @Translation("Reviews"),
 *   handlers = {
 *     "storage" = "Drupal\lei_core\ReviewStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lei_core\ReviewListBuilder",
 *     "views_data" = "Drupal\lei_core\Entity\ReviewViewsData",
 *     "translation" = "Drupal\lei_core\ReviewTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\lei_core\Form\ReviewForm",
 *       "add" = "Drupal\lei_core\Form\ReviewForm",
 *       "edit" = "Drupal\lei_core\Form\ReviewForm",
 *       "delete" = "Drupal\lei_core\Form\ReviewDeleteForm",
 *     },
 *     "access" = "Drupal\lei_core\ReviewAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\lei_core\ReviewHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "review",
 *   data_table = "review_field_data",
 *   revision_table = "review_revision",
 *   revision_data_table = "review_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer review entities",
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
 *     "canonical" = "/review/{review}",
 *     "add-form" = "/review/add",
 *     "edit-form" = "/review/{review}/edit",
 *     "delete-form" = "/review/{review}/delete",
 *     "version-history" = "/review/{review}/revisions",
 *     "revision" = "/review/{review}/revisions/{review_revision}/view",
 *     "revision_revert" = "/admin/content/review/{review}/revisions/{revision}/revert",
 *     "revision_delete" = "/admin/content/review/{review}/revisions/{revision}/delete",
 *     "translation_revert" = "/admin/content/review/{review}/revisions/{revision}/revert/{langcode}",
 *     "collection" = "/admin/content/review",
 *   },
 *   field_ui_base_route = "review.settings"
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
