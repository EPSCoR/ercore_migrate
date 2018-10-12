<?php

namespace Drupal\ercore_migrate\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Class PostMigrationSubscriber.
 *
 * Run our user flagging after the last node migration is run.
 *
 * @package Drupal\core_migrate
 */
class PostMigrationSubscriber implements EventSubscriberInterface {

  /**
   * Get subscribed events.
   *
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_IMPORT][] = ['onMigratePostImport'];
    return $events;
  }

  /**
   * Check for our last node migration and run our paragraph connect.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function onMigratePostImport(MigrateImportEvent $event) {
    if ($event->getMigration()->getBaseId() == 'ercore_event') {
      $this->connectEngagements();
    }
  }

  /**
   * Connects all engagements to nodes.
   */
  private function connectEngagements() {
    $entities = self::getIds();
    foreach ($entities as $entity) {
      $paragraph = Paragraph::load($entity->entity_id);
      Node::load($entity->field_er_cal_entity_reference_target_id)
        ->set('field_ercore_ev_engagement', $paragraph)
        ->save();
    }
  }

  /**
   * Receives Node Id, returns Node ID of referencing Engagement.
   *
   * @return array
   *   Returns array of engagements.
   */
  public function getIds() {
    $query = Database::getConnection('default', 'migration')
      ->select('field_data_field_er_cal_entity_reference', 'n')
      ->fields('n', ['entity_id', 'field_er_cal_entity_reference_target_id'])
      ->condition('n.bundle', 'er_engagement');
    return $query->execute()->fetchAll();
  }

}
