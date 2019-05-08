<?php

namespace Drupal\lei_core;

use Drupal;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\lei_core\EntityInterface;
use Drupal\lei_core\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityControllerBase.
 *
 *  Returns responses for entity routes.
 */
abstract class EntityControllerBase extends ControllerBase implements ContainerInjectionInterface
{
  /**
   * @var Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter = NULL;

  /**
   * EntityControllerBase constructor.
   * @param Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   */
  public function __construct($date_formatter)
  {
    $this->dateFormatter = $date_formatter;
  }

  static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * @return int
   */
  abstract protected function getEntityTypeId();

  /**
   * Displays a revision.
   *
   * @param int $revision The revision ID.
   * @return array An array suitable for drupal_render().
   */
  public function revisionShow($revision)
  {
    /** @var EntityInterface $entity */
    $entity = $this->entityTypeManager()->getStorage($this->getEntityTypeId())->loadRevision($revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder($this->getEntityTypeId());

    return $view_builder->view($entity);
  }

  /**
   * Page title callback for a revision.
   *
   * @param int $revision The revision ID.
   * @return string The page title.
   */
  public function revisionPageTitle($revision)
  {
    /** @var EntityInterface $entity */
    $entity = $this->entityTypeManager()->getStorage($this->getEntityTypeId())->loadRevision($revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity->label(), '%date' => $this->dateFormatter->format($entity->getRevisionCreationTime())]);
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
    $has_translations = (count($languages) > 1);
    /** @var EntityStorageInterface $entity_storage */
    $entity_storage = $this->entityTypeManager()->getStorage($this->getEntityTypeId());

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity->label()]) : $this->t('Revisions for %title', ['%title' => $entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all restaurant revisions") || $account->hasPermission('administer restaurant entities')));
    $delete_permission = (($account->hasPermission("delete all restaurant revisions") || $account->hasPermission('administer restaurant entities')));

    $rows = [];

    $vids = $entity_storage->revisionIds($entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var EntityInterface $revision */
      $revision = $entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity->getRevisionId()) {
          $link = new Link($date, $revision->toUrl());
        } else {
          $link = $entity->toLink($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        } else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
                $revision->toUrl('translation_revert') : $revision->toUrl('revision_revert'),
            ];
          }

          if ($delete_permission) {
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
        }

        $rows[] = $row;
      }
    }

    $build['restaurant_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
