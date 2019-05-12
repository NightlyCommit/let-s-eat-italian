<?php

namespace Drupal\lei_entity\Form;

use Drupal;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\lei_entity\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityTypeSettingsForm.
 *
 * @ingroup lei_entity
 */
class EntityTypeForm extends ConfigFormBase
{
  /** @var EntityTypeInterface */
  protected $entityType;

  /** @var EntityTypeManagerInterface */
  protected $entityTypeManager;

  /** @var ModuleHandlerInterface */
  protected $moduleHandler;

  /** @var EntityFieldManagerInterface */
  protected $entityFieldManager;

  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, EntityFieldManagerInterface $entity_field_manager)
  {
    parent::__construct($config_factory);

    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->entityFieldManager = $entity_field_manager;
  }

  static public function create(ContainerInterface $container)
  {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames()
  {
    return [];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId()
  {
    return 'lei_entity_type_settings';
  }

  /**
   * Defines the settings form for entities.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @param string $entity_type_id
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL)
  {
    $this->entityType = $this->entityTypeManager->getDefinition($entity_type_id);

    $form['admin_theme']['use_admin_theme'] = [
      '#type' => 'checkbox',
      '#title' => t('Use the administration theme when editing or creating content'),
      '#description' => t('Control which roles can "View the administration theme" on the <a href=":permissions">Permissions page</a>.', [':permissions' => Url::fromRoute('user.admin_permissions')->toString()]),
      '#default_value' => Drupal::configFactory()->getEditable($entity_type_id . '.settings')->get('use_admin_theme'),
    ];

    $form['additional_settings'] = [
      '#type' => 'vertical_tabs',
      '#attached' => [
        'library' => ['node/drupal.content_types'],
      ],
    ];

    if ($this->moduleHandler->moduleExists('language')) {
      $form['language'] = [
        '#type' => 'details',
        '#title' => t('Language settings'),
        '#group' => 'additional_settings',
      ];

      $language_configuration = ContentLanguageSettings::loadByEntityTypeBundle($entity_type_id, $entity_type_id);

      $form['language']['language_configuration'] = [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => $entity_type_id,
          'bundle' => $entity_type_id,
        ],
        '#default_value' => $language_configuration,
      ];
    }

    $actions = [
      '#type' => 'actions'
    ];

    $actions['save'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Save'),
      '#submit' => [
        '::save',
        'language_configuration_element_submit',
        'content_translation_language_configuration_element_submit',
        '::submitForm'
      ],
    ];

    $form['actions'] = $actions;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {
    $entityType = $this->entityType;

    $this->configFactory()->getEditable($entityType->id() . '.settings')
      ->set('use_admin_theme', $form_state->getValue('use_admin_theme'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('%label settings have been saved.', [
      '%label' => $this->entityType->getLabel()
    ]));
  }
}
