<?php

namespace Drupal\adimeo_test\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\Entity\Node;

/**
 * A Queue Worker that unpublishes expired events.
 *
 * @QueueWorker(
 *   id = "event_unpublish_queue_worker",
 *   title = @Translation("Event Unpublish Queue Worker"),
 *   cron = {"time" = 60}
 * )
 */
class EventUnpublishQueueWorker extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $node = Node::load($data['nid']);
    $current_date = date('Y-m-d\TH:i:s');

    if ($node instanceof Node && $node->isPublished() && $node->get('field_date_range')->end_value < $current_date) {
      $node->setUnpublished();
      $node->save();
    }
  }

}
