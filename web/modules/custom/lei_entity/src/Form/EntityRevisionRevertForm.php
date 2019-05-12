<?php

namespace Drupal\lei_entity\Form;

use Drupal;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\lei_entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting an entity revision.
 *
 * @ingroup lei_entity
 */
class EntityRevisionRevertForm extends ConfirmFormBase
{

  /**
   * The entity revision.
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
   * The date formatter service.
   *
   * @var DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new form.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param DateFormatterInterface $date_formatter
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, DateFormatterInterface $date_formatter)
  {
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'entity_revision_revert_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return t('Are you sure you want to revert to the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime())
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl()
  {
    return $this->revision->toUrl('version-history');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText()
  {
    return t('Revert');
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
    // The revision timestamp will be updated when the revision is saved. Keep the original one for the confirmation message.
    $original_revision_timestamp = $this->revision->getRevisionCreationTime();

    $this->revision = $this->prepareRevertedRevision($this->revision, $form_state);
    $this->revision->setRevisionLogMessage(t('Copy of the revision from %date.', [
      '%date' => $this->dateFormatter->format($original_revision_timestamp)
    ]));
    $this->revision->save();

    $entityTypeLabel = $this->revision->getEntityType()->getLabel();

    $this->logger('content')->notice($entityTypeLabel . ': reverted %title revision %revision.', [
      '%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()
    ]);

    $this
      ->messenger()
      ->addStatus(t($entityTypeLabel . ' %title has been reverted to the revision from %revision-date.', [
        '%title' => $this->revision->label(), '%revision-date' => $this->dateFormatter->format($original_revision_timestamp)
      ]));

    $form_state->setRedirectUrl($this->revision->toUrl('version-history'));
  }

  /**
   * Prepares a revision to be reverted.
   *
   * @param EntityInterface $revision
   * @param FormStateInterface $form_state
   *
   * @return EntityInterface
   */
  protected function prepareRevertedRevision(EntityInterface $revision, FormStateInterface $form_state)
  {
    $revision->setNewRevision();
    $revision->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime(Drupal::time()->getRequestTime());

    return $revision;
  }

}
