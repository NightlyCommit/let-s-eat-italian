<?php

namespace Drupal\lei_entity\Form;

use Drupal;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\lei_entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for entity edit forms.
 *
 * @ingroup lei_entity
 */
class EntityForm extends ContentEntityForm
{

  /**
   * The Current User object.
   *
   * @var AccountInterface
   */
  protected $currentUser;

  /**
   * The date formatter service.
   *
   * @var DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a NodeForm object.
   *
   * @param EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param TimeInterface $time
   *   The time service.
   * @param AccountInterface $current_user
   *   The current user.
   * @param DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, AccountInterface $current_user = NULL, DateFormatterInterface $date_formatter = NULL)
  {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);

    $this->currentUser = $current_user;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('current_user'),
      $container->get('date.formatter')
    );
  }

  /**
   * @param RouteMatchInterface $route_match
   * @param string $entity_type_id
   * @return \Drupal\Core\Entity\EntityInterface|mixed|null
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id)
  {
    $entity = $route_match->getParameter('entity');

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state)
  {
    /** @var EntityInterface $entity */
    $entity = $this->entity;

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('Edit %type @title', [
        '%type' => strtolower($entity->getEntityType()->getLabel()),
        '@title' => $entity->label(),
      ]);
    }

    // Changed must be sent to the client, for later overwrite error checking.
    $form['changed'] = [
      '#type' => 'hidden',
      '#default_value' => $entity->getChangedTime(),
    ];

    $form = parent::form($form, $form_state);

    $form['advanced']['#type'] = 'container';
    $form['advanced']['#attributes']['class'][] = 'entity-meta';

    $form['meta'] = [
      '#type' => 'container',
      '#group' => 'advanced',
      '#weight' => -10,
      '#title' => $this->t('Status'),
      '#attributes' => ['class' => ['entity-meta__header']],
      '#tree' => TRUE,
    ];

    $form['meta']['published'] = [
      '#type' => 'item',
      '#markup' => $entity->isPublished() ? $this->t('Published') : $this->t('Not published'),
      '#access' => !$entity->isNew(),
      '#wrapper_attributes' => ['class' => ['entity-meta__title']],
    ];

    $form['meta']['changed'] = [
      '#type' => 'item',
      '#title' => $this->t('Last saved'),
      '#markup' => !$entity->isNew() ? $this->dateFormatter->format($entity->getChangedTime(), 'short') : $this->t('Not saved yet'),
      '#wrapper_attributes' => ['class' => ['container-inline']],
    ];

    $form['meta']['author'] = [
      '#type' => 'item',
      '#title' => $this->t('Author'),
      '#markup' => $entity->getOwner()->getAccountName(),
      '#wrapper_attributes' => ['class' => ['container-inline']],
    ];

    $form['revision_information']['#type'] = 'container';
    $form['revision_information']['#group'] = 'meta';

    $form['status']['#group'] = 'footer';

    $form['author'] = [
      '#type' => 'details',
      '#title' => t('Authoring information'),
      '#group' => 'advanced',
      '#attributes' => [
        'class' => ['lei_entity-form-author'],
      ],
      '#weight' => 90,
      '#optional' => TRUE,
    ];

    if (isset($form['uid'])) {
      $form['uid']['#group'] = 'author';
    }

    if (isset($form['created'])) {
      $form['created']['#group'] = 'author';
    }

    $form['#theme'] = 'lei_entity_edit_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {
    $entity = $this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('revision') && $form_state->getValue('revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(Drupal::time()->getRequestTime());
      $entity->setRevisionUserId(Drupal::currentUser()->id());
    } else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);
    $entity_type_abel = strtolower($this->getEntity()->getEntityType()->getLabel());

    switch ($status) {
      case SAVED_NEW:
        $this
          ->messenger
          ->addStatus($this->t('Created the %label @type.', [
            '%label' => $entity->label(),
            '@type' => $entity_type_abel
          ]));
        break;

      default:
        $this
          ->messenger
          ->addStatus($this->t('Saved the %label @type.', [
            '%label' => $entity->label(),
            '@type' => $entity_type_abel
          ]));
    }

    $form_state->setRedirectUrl($entity->toUrl());
  }
}
