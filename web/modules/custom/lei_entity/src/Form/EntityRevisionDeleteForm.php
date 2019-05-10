<?php

namespace Drupal\lei_entity\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\lei_entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a revision.
 *
 * @ingroup lei_entity
 */
class EntityRevisionDeleteForm extends ConfirmFormBase
{


  /**
   * The Restaurant revision.
   *
   * @var \Drupal\lei_core\Entity\RestaurantInterface
   */
  protected $revision;

  /**
   * The entity.
   *
   * @var \Drupal\lei_entity\EntityInterface
   */
  protected $entity;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new form.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager)
  {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'restaurant_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return t('Are you sure you want to delete the revision from %revision-date?', ['%revision-date' => format_date($this->revision->getRevisionCreationTime())]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl()
  {
    return $this->entity->toUrl('version-history');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText()
  {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $entity = NULL, $entity_revision = NULL)
  {
    $this->entity = $entity;
    $this->revision = $this->entityTypeManager->getStorage($entity->getEntityTypeId())->loadRevision($entity_revision);

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $entity_type = $this->entity->getEntityType();
    $this->entityTypeManager->getStorage($entity_type->id())->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice($entity_type->getLabel() . ': deleted %title revision %revision.', [
      '%title' => $this->revision->label(),
      '%revision' => $this->revision->getRevisionId()
    ]);

    drupal_set_message(t('Revision from %revision-date of @label %title has been deleted.', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
      '@label' => $entity_type->getLabel(),
      '%title' => $this->revision->label()
    ]));

    $form_state->setRedirectUrl($this->entity->toUrl());

    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {' . $entity_type->getRevisionDataTable() . '} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirectUrl($this->entity->toUrl('version-history'));
    }
  }
}
