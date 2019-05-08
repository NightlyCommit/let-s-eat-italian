<?php

namespace Drupal\lei_restaurant\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\lei_restaurant\Entity\RestaurantInterface;

/**
 * Class RestaurantController.
 *
 *  Returns responses for Restaurant routes.
 */
class RestaurantController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Restaurant  revision.
   *
   * @param int $restaurant_revision
   *   The Restaurant  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($restaurant_revision) {
    $restaurant = $this->entityManager()->getStorage('restaurant')->loadRevision($restaurant_revision);
    $view_builder = $this->entityManager()->getViewBuilder('restaurant');

    return $view_builder->view($restaurant);
  }

  /**
   * Page title callback for a Restaurant  revision.
   *
   * @param int $restaurant_revision
   *   The Restaurant  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($restaurant_revision) {
    $restaurant = $this->entityManager()->getStorage('restaurant')->loadRevision($restaurant_revision);
    return $this->t('Revision of %title from %date', ['%title' => $restaurant->label(), '%date' => format_date($restaurant->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Restaurant .
   *
   * @param \Drupal\lei_restaurant\Entity\RestaurantInterface $restaurant
   *   A Restaurant  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(RestaurantInterface $restaurant) {
    $account = $this->currentUser();
    $langcode = $restaurant->language()->getId();
    $langname = $restaurant->language()->getName();
    $languages = $restaurant->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $restaurant_storage = $this->entityManager()->getStorage('restaurant');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $restaurant->label()]) : $this->t('Revisions for %title', ['%title' => $restaurant->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all restaurant revisions") || $account->hasPermission('administer restaurant entities')));
    $delete_permission = (($account->hasPermission("delete all restaurant revisions") || $account->hasPermission('administer restaurant entities')));

    $rows = [];

    $vids = $restaurant_storage->revisionIds($restaurant);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\lei_restaurant\RestaurantInterface $revision */
      $revision = $restaurant_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $restaurant->getRevisionId()) {
          $link = $this->l($date, new Url('entity.restaurant.revision', ['restaurant' => $restaurant->id(), 'restaurant_revision' => $vid]));
        }
        else {
          $link = $restaurant->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
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
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.restaurant.translation_revert', ['restaurant' => $restaurant->id(), 'restaurant_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.restaurant.revision_revert', ['restaurant' => $restaurant->id(), 'restaurant_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.restaurant.revision_delete', ['restaurant' => $restaurant->id(), 'restaurant_revision' => $vid]),
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
