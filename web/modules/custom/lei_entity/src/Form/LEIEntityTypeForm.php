<?php

namespace Drupal\lei_entity\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityTypeSettingsForm.
 *
 * @ingroup lei_entity
 */
class LEIEntityTypeForm extends ConfigFormBase
{

  /** @var EntityTypeManagerInterface */
  protected $entityTypeManager;

  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager)
  {
    parent::__construct($config_factory);

    $this->entityTypeManager = $entity_type_manager;
  }

  static public function create(ContainerInterface $container)
  {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
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
    // TODO: Implement getEditableConfigNames() method.
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
   * Defines the settings form for Restaurant entities.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param string $entity_type_id
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL)
  {
    $form[$this->getFormId()]['#markup'] = $this->t('Settings form for %label entities. Manage field settings here.', [
      '%label' => $this->entityTypeManager->getDefinition($entity_type_id)->getLabel()
    ]);

    return $form;
  }
}
