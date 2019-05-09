<?php

namespace Drupal\lei_entity\Controller;

use Drupal;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityController.
 *
 * Returns responses for entity routes.
 */
class EntityController extends ControllerBase implements ContainerInjectionInterface
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

  static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * Displays a revision.
   *
   * @param int $revision The revision ID.
   * @return array An array suitable for drupal_render().
   */
  public function revisionShow($revision)
  {
    /** @var Drupal\lei_entity\EntityInterface $entity */
    $entity = $this->entityTypeManager()->getStorage($this->getEntityTypeId())->loadRevision($revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder($entity->getEntityTypeId());

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
    /** @var Drupal\lei_entity\EntityInterface $entity */
    $entity = $this->entityTypeManager()->getStorage($this->getEntityTypeId())->loadRevision($revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity->label(), '%date' => $this->dateFormatter->format($entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of an entity .
   *
   * @param Drupal\lei_entity\EntityInterface $entity An entity.
   * @return array An array as expected by drupal_render().
   */
  public function revisionOverview(Drupal\lei_entity\EntityInterface $entity)
  {
    $account = $this->currentUser();
    $langcode = $entity->language()->getId();
    $langname = $entity->language()->getName();
    $languages = $entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);

    $entityTypeId = $entity->getEntityTypeId();

    /** @var Drupal\lei_entity\EntityStorageInterface $entity_storage */
    $entity_storage = $this->entityTypeManager()->getStorage($entityTypeId);

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity->label()]) : $this->t('Revisions for %title', ['%title' => $entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all ' . $entityTypeId . ' revisions") || $account->hasPermission('administer ' . $entityTypeId . ' entities')));
    $delete_permission = (($account->hasPermission("delete all ' . $entityTypeId . ' revisions") || $account->hasPermission('administer ' . $entityTypeId . ' entities')));

    $rows = [];

    $vids = $entity_storage->revisionIds($entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var Drupal\lei_entity\EntityInterface $revision */
      $revision = $entity_storage->loadRevision($vid);

      // Only show revisions that are affected by the language that is being displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');

        if (!$revision->isDefaultRevision()) {
          $link = $revision->toLink($date, 'revision', [
            'entity' => $entity->id(),
            'entity_revision' => $vid
          ])->toString();
        } else {
          $link = $revision->toLink($date)->toString();
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

    $build['entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }
}
