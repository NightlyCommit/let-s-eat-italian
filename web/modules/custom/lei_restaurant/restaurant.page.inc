<?php

/**
 * @file
 * Contains restaurant.page.inc.
 *
 * Page callback for Restaurant entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Restaurant templates.
 *
 * Default template: restaurant.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_restaurant(array &$variables) {
  // Fetch Restaurant Entity Object.
  $restaurant = $variables['elements']['#restaurant'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
