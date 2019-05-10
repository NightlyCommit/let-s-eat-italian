<?php

namespace Drupal\lei_entity\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\lei_entity\EntityInterface;
use Drupal\lei_entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityController.
 *
 * Returns responses for entity routes.
 */
class EntityController extends ControllerBase implements ContainerInjectionInterface
{
  /**
   * @var DateFormatterInterface
   */
  protected $dateFormatter = NULL;

  /**
   * @var RendererInterface
   */
  protected $renderer;

  /**
   * EntityControllerBase constructor.
   *
   * @param DateFormatterInterface $date_formatter
   * @param RendererInterface $renderer
   */
  public function __construct(DateFormatterInterface $date_formatter, RendererInterface $renderer)
  {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a revision.
   *
   * @param EntityInterface $entity
   * @param int $entity_revision The revision ID.
   *
   * @return array An array suitable for drupal_render().
   */
  public function revisionShow($entity, $entity_revision)
  {
    /** @var EntityInterface $revision */
    $revision = $this->entityTypeManager()->getStorage($entity->getEntityTypeId())->loadRevision($entity_revision);

    $view_builder = $this->entityTypeManager()->getViewBuilder($revision->getEntityTypeId());

    return $view_builder->view($revision);
  }

  /**
   * Page title callback for a revision.
   *
   * @param EntityInterface $entity
   * @param int $entity_revision The revision ID.
   *
   * @return string The page title.
   */
  public function revisionPageTitle($entity, $entity_revision)
  {
    /** @var EntityInterface $revision */
    $revision = $this->entityTypeManager()->getStorage($entity->getEntityTypeId())->loadRevision($entity_revision);

    return $this->t('Revision of %title from %date', [
      '%title' => $entity->label(),
      '%date' => $this->dateFormatter->format($revision->getRevisionCreationTime())
    ]);
  }

  /**
   * Generates an overview table of older revisions of an entity .
   *
   * @param EntityInterface $entity An entity.
   * @return array An array as expected by drupal_render().
   */
  public function revisionOverview(EntityInterface $entity)
  {
    $account = $this->currentUser();
    $langcode = $entity->language()->getId();
    $langname = $entity->language()->getName();
    $languages = $entity->getTranslationLanguages();
    $hasTranslations = (count($languages) > 1);

    $entityTypeId = $entity->getEntityTypeId();

    /** @var EntityStorageInterface $entityStorage */
    $entityStorage = $this->entityTypeManager()->getStorage($entityTypeId);

    $build['#title'] = $hasTranslations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity->label()]) : $this->t('Revisions for %title', ['%title' => $entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revertPermission = (($account->hasPermission("revert $entityTypeId revisions") || $account->hasPermission('revert all revisions') || $account->hasPermission('administer ' . $entityTypeId . 'entities')) && $entity->access('update'));
    $deletePermission = (($account->hasPermission("delete $entityTypeId revisions") || $account->hasPermission('delete all revisions') || $account->hasPermission('administer ' . $entityTypeId . 'entities')) && $entity->access('delete'));

    $rows = [];
    $defaultRevision = $entity->getRevisionId();
    $currentRevisionDisplayed = FALSE;

    foreach ($this->getRevisionIds($entity, $entityStorage) as $vid) {
      /** @var EntityInterface $revision */
      $revision = $entityStorage->loadRevision($vid);

      // Only show revisions that are affected by the language that is being displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');

        // We treat also the latest translation-affecting revision as current revision, if it was the default revision, as its values for the current language will be the same of the current default revision in this case.
        $is_current_revision = $vid == $defaultRevision || (!$currentRevisionDisplayed && $revision->wasDefaultRevision());
        if (!$is_current_revision) {
          $link = $revision->toLink($date, 'revision')->toString();
        } else {
          $link = $entity->toLink($date)->toString();
          $currentRevisionDisplayed = TRUE;
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];

        $this->renderer->addCacheableDependency($column['data'], $username);

        $row[] = $column;

        if ($is_current_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];

          $rows[] = [
            'data' => $row,
            'class' => ['revision-current'],
          ];
        } else {
          $links = [];

          if ($revertPermission) {
            $links['revert'] = [
              'title' => $vid < $entity->getRevisionId() ? $this->t('Revert') : $this->t('Set as current revision'),
              'url' => $hasTranslations ? $revision->toUrl('translation_revert') : $revision->toUrl('revision_revert'),
            ];
          }

          if ($deletePermission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => $revision->toUrl('revision_delete'),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];

          $rows[] = $row;
        }
      }
    }

    $build['entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header
    ];

    $build['pager'] = ['#type' => 'pager'];

    return $build;
  }

  /**
   * Gets a list of revision IDs for a specific entity.
   *
   * @param EntityInterface $entity
   * @param EntityStorageInterface $entityStorage
   *
   * @return int[] Revision IDs (in descending order).
   */
  protected function getRevisionIds(EntityInterface $entity, EntityStorageInterface $entityStorage)
  {
    $result = $entityStorage->getQuery()
      ->allRevisions()
      ->condition($entity->getEntityType()->getKey('id'), $entity->id())
      ->sort($entity->getEntityType()->getKey('revision'), 'DESC')
      ->pager(50)
      ->execute();
    return array_keys($result);
  }
}
