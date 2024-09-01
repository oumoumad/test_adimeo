<?php

namespace Drupal\adimeo_test\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Cache\Cache;
use Drupal\node\NodeInterface;

/**
 * Provides a 'Related Events' Block.
 *
 * @Block(
 *   id = "related_events_block",
 *   admin_label = @Translation("Related Events")
 * )
 */
class EventRelatedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $output = [];

    if ($node instanceof NodeInterface && $node->bundle() == 'event') {
      $type_tid = $node->get('field_event_type')->target_id;
      $current_date = date('Y-m-d\TH:i:s');

      // Query to retrieve events of the same type
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'event')
        ->condition('field_event_type', $type_tid)
        ->condition('field_date_range.end_value', $current_date, '>=')
        ->sort('field_date_range.value', 'ASC')
        ->range(0, 3)
        ->accessCheck(TRUE);

      $nids = $query->execute();
      $related_nodes = Node::loadMultiple($nids);

      // If fewer than 3 events are found, fill with events of other types
      if (count($related_nodes) < 3) {
        $query = \Drupal::entityQuery('node')
          ->condition('type', 'event')
          ->condition('field_event_type', $type_tid, '<>')
          ->condition('field_date_range.end_value', $current_date, '>=')
          ->sort('field_date_range.value', 'ASC')
          ->range(0, 3 - count($related_nodes))
          ->accessCheck(TRUE);

        $other_nids = $query->execute();
        $related_nodes += Node::loadMultiple($other_nids);
      }

      // Render the events in teaser view mode
      foreach ($related_nodes as $related_node) {
        $output[] = \Drupal::entityTypeManager()
          ->getViewBuilder('node')
          ->view($related_node, 'teaser');
      }

      // Add cache metadata to the block
      $output['#cache'] = [
        'contexts' => [
          'url.path',  // Cache varies by the current URL path (important for blocks displayed on specific nodes)
        ],
        'tags' => [
          'node:' . $node->id(),  // Cache invalidates when this node is updated
          'taxonomy_term:' . $type_tid,  // Cache invalidates when this taxonomy term is updated
        ],
        'max-age' => Cache::PERMANENT,  // Cache indefinitely, until explicitly invalidated by tags
      ];
    }

    if (empty($output)) {
      $output['#markup'] = $this->t('No related events found.');
    }

    return $output;
  }
}
