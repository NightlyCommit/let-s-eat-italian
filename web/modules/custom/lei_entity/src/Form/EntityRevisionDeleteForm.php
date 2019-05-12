<?php

namespace Drupal\lei_entity\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
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
   * The revision.
   *
   * @var EntityInterface
   */
  protected $revision;

  /**
   * The entity.
   *
   * @var EntityInterface
   */
  protected $entity;

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database connection.
   *
   * @var Connection
   */
  protected $connection;

  /**
   * The date formatter service.
   *
   * @var DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new form.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param Connection $connection
   * @param DateFormatterInterface $date_formatter
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection, DateFormatterInterface $date_formatter)
  {
    $this->entityTypeManager = $entity_type_manager;
    $this->connection = $connection;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('database'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'entity_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL)
  {
    $route_match = $this->getRouteMatch();

    $this->entity = $route_match->getParameter($entity_type_id);
    $this->revision = $this->entityTypeManager->getStorage($this->entity->getEntityTypeId())->loadRevision($route_match->getParameter($entity_type_id . '_revision'));

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

    $this
      ->messenger()
      ->addStatus(t('Revision from %revision-date of @label %title has been deleted.', [
        '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
        '@label' => $entity_type->getLabel(),
        '%title' => $this->revision->label()
      ]));

    $form_state->setRedirectUrl($this->entity->toUrl());

    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {' . $entity_type->getRevisionDataTable() . '} WHERE id = :id', [
        ':id' => $this->revision->id()
      ])->fetchField() > 1) {
      $form_state->setRedirectUrl($this->entity->toUrl('version-history'));
    }
  }
}
