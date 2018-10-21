<?php

namespace Drupal\ercore_migrate\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\user\Entity\User;
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
    elseif ($event->getMigration()->getBaseId() == 'ercore_collaboration') {
      $this->connectCollaborators();
    }
  }

  /**
   * Connects all engagements to nodes.
   */
  private function connectEngagements() {
    $entities = self::getEngagement();
    foreach ($entities as $entity) {
      $paragraph = Paragraph::load($entity->entity_id);
      Node::load($entity->field_er_cal_entity_reference_target_id)
        ->set('field_ercore_ev_engagement', $paragraph)
        ->save();
    }
  }

  /**
   * Connects all engagements to nodes.
   */
  private function connectCollaborators() {
    $entities = self::getCollaborations();
    foreach ($entities as $entity) {
      $users = [];
      $collaborators = [];
      $ids = self::getCollaboratorIds($entity);
      if (!empty($ids)) {
        $filtered = self::filterParticipants($ids);
        foreach ($filtered as $id => $type) {
          if ($type === 'Participant') {
            $users[] = User::load(self::getUser($id));
          }
          elseif ($type === 'Collaborator') {
            $collaborators[] = Paragraph::load($id);
          }
        }
        Node::load($entity)
          ->set('field_ercore_organizer', $users)
          ->set('field_ercore_cn_collaborator', $collaborators)
          ->save();
      }
    }
  }

  /**
   * Receives Node ID, returns Node ID of referencing Engagement.
   *
   * @return array
   *   Returns array of engagements.
   */
  public function getEngagement() {
    $query = Database::getConnection('default', 'migration')
      ->select('field_data_field_er_cal_entity_reference', 'n')
      ->fields('n', ['entity_id', 'field_er_cal_entity_reference_target_id'])
      ->condition('n.bundle', 'er_engagement');
    return $query->execute()->fetchAll();
  }

  /**
   * Receives Node ID, returns Node ID of referencing Collaborator.
   *
   * @return array
   *   Returns array of engagements.
   */
  public function getCollaborations() {
    $query = Database::getConnection('default', 'default')
      ->select('node', 'n')
      ->fields('n', ['nid'])
      ->condition('n.type', 'ercore_collaboration');
    return $query->execute()->fetchCol();
  }

  /**
   * Receives Node ID, returns Node IDs of referencing Collaborators.
   *
   * @param int $nid
   *   Node IDs.
   *
   * @return array
   *   Returns array of collaborator IDs.
   */
  public function getCollaboratorIds($nid) {
    $query = Database::getConnection('default', 'migration')
      ->select('field_data_field_er_collab_ref', 'n')
      ->fields('n', ['entity_id'])
      ->condition('n.field_er_collab_ref_target_id', $nid);
    return $query->execute()->fetchAllKeyed(0, 0);
  }

  /**
   * Receives Node IDs, filters by collaboration status.
   *
   * @param array $nids
   *   Node IDs.
   *
   * @return array
   *   Returns array of engagements.
   */
  public function filterParticipants(array $nids) {
    $query = Database::getConnection('default', 'migration')
      ->select('field_data_field_er_collab_user_status', 'e')
      ->fields('e', ['entity_id', 'field_er_collab_user_status_value'])
      ->condition('e.entity_id', $nids, 'IN');
    return $query->execute()->fetchAllKeyed(0, 1);
  }

  /**
   * Receives ID, returns User ID of Collaborator.
   *
   * @param int $id
   *   Node ID.
   *
   * @return int
   *   Returns ID of User.
   */
  public function getUser($id) {
    $query = Database::getConnection('default', 'migration')
      ->select('field_data_field_er_user_lookup', 'd')
      ->fields('d', ['field_er_user_lookup_target_id'])
      ->condition('d.entity_id', $id);
    return $query->execute()->fetchField();
  }

}
