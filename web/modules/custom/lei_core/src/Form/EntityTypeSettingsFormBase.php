<?php

namespace Drupal\lei_core\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityTypeSettingsForm.
 *
 * @ingroup lei_core
 */
abstract class EntityTypeSettingsFormBase extends FormBase
{

  /** @var EntityTypeManagerInterface */
  protected $entityTypeManager;

  /** @var string */
  protected $entityTypeId;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, string $entity_type_id)
  {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeId = $entity_type_id;
  }

  static public function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function getFormId()
  {
    return $this->entityTypeId . '_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Empty implementation of the abstract submit class.
  }

  /**
   * Defines the settings form for Restaurant entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form[$this->getFormId()]['#markup'] = $this->t('Settings form for %label entities. Manage field settings here.', [
      '%label' => $this->entityTypeManager->getDefinition($this->entityTypeId)->getLabel()
    ]);

    return $form;
  }
}
