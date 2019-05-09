<?php

namespace Drupal\lei_entity\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\lei_entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting an entity revision.
 *
 * @ingroup lei_entity
 */
abstract class EntityRevisionRevertFormBase extends ConfirmFormBase
{

  /**
   * The entity revision.
   *
   * @var \Drupal\lei_entity\EntityInterface
   */
  protected $revision;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * @return string
   */
  abstract static function entityTypeId();

  /**
   * Constructs a new form.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   */
  public function __construct(EntityStorageInterface $entity_storage, DateFormatterInterface $date_formatter)
  {
    $this->entityStorage = $entity_storage;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity.manager')->getStorage(static::entityTypeId()),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return static::entityTypeId() . '_revision_revert_confirm';
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
    return new Url('entity.' . $this->revision->getEntityTypeId() . '.version_history', [
      $this->revision->getEntityTypeId() => $this->revision->id()
    ]);
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
  public function getDescription()
  {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $revision = NULL)
  {
    $this->revision = $this->entityStorage->loadRevision($revision);

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

    drupal_set_message(t($entityTypeLabel . ' %title has been reverted to the revision from %revision-date.', [
      '%title' => $this->revision->label(), '%revision-date' => $this->dateFormatter->format($original_revision_timestamp)
    ]));

    $form_state->setRedirect(
      'entity.' . $this->revision->getEntityTypeId() . '.version_history',
      [$this->revision->getEntityTypeId() => $this->revision->id()]
    );
  }

  /**
   * Prepares a revision to be reverted.
   *
   * @param \Drupal\lei_entity\EntityInterface $revision
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\lei_entity\EntityInterface
   */
  protected function prepareRevertedRevision(EntityInterface $revision, FormStateInterface $form_state)
  {
    $revision->setNewRevision();
    $revision->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime(\Drupal::time()->getRequestTime());

    return $revision;
  }

}
