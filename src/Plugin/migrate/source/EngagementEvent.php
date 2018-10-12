<?php

namespace Drupal\ercore_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Source plugin for the Users.
 *
 * @MigrateSource(
 *   id = "engagement_event"
 * )
 */
class EngagementEvent extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n');
    $query->fields('n', [
      'nid',
      'vid',
      'title',
      'uid',
      'status',
      'created',
      'changed',
    ]);
    $query->condition('n.type', 'er_event');

    // Dates.
    $query->addField('date', 'field_er_cal_event_date_value', 'date_start');
    $query->addField('date', 'field_er_cal_event_date_value2', 'date_end');
    $query->leftJoin('field_data_field_er_cal_event_date', 'date', "date.entity_id = n.nid");

    // Components.
    $query->addField('c', 'field_er_components_target_id', 'component');
    $query->leftJoin('field_data_field_er_components', 'c', "c.entity_id = n.nid AND c.bundle = :bundle",
      [':bundle' => 'er_event']
    );

    // Organizers.
    $query->addField('o', 'field_er_user_entity_reference_target_id', 'organizers');
    $query->leftJoin('field_data_field_er_user_entity_reference', 'o', "o.entity_id = n.nid");

    // Url.
    $query->addField('u', 'field_er_url_value', 'url');
    $query->leftJoin('field_data_field_er_url', 'u', "u.entity_id = n.nid");

    // Body.
    $query->addField('b', 'body_value', 'body');
    $query->leftJoin('field_data_body', 'b', "b.entity_id = n.nid");

    // Type.
    $query->addField('t', 'field_er_event_type_value', 'type');
    $query->leftJoin('field_data_field_er_event_type', 't', "t.entity_id = n.nid");

    // EPSCoR.
    $query->addField('e', 'field_er_event_reminders_value', 'epscor');
    $query->leftJoin('field_data_field_er_event_reminders', 'e', "e.entity_id = n.nid");
    
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'uid' => $this->t('User ID'),
      'title' => $this->t('Node Title'),
      'created' => $this->t('Created Time'),
      'changed' => $this->t('Changed Time'),
      'status' => $this->t('Status'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Check for end date matching start date.
    $start = $row->getSourceProperty('date_start');
    $end = $row->getSourceProperty('date_end');
    if ($start === $end) {
      $row->setSourceProperty('date_end', NULL);
    }
    else {
      $row->setSourceProperty('date_end', self::processDates($end));
    }
    $row->setSourceProperty('date_start', self::processDates($start));

    return parent::prepareRow($row);
  }

  /**
   * Process user account dates.
   *
   * @param string $date
   *   Receive dates in Drupal 7 format.
   *
   * @return string|null
   *   Returns string.
   */
  public function processDates($date) {
    return str_replace(' ', 'T', $date);
  }

  /**
   * Receives Node Id, returns Node ID of referencing Engagement.
   *
   * @param int $eid
   *   Receives Entity ID of original node.
   *
   * @return int
   *   Returns integer ID of related node.
   */
  public function getEngagement($eid) {
    $query = Database::getConnection()
      ->select('migrate_map_ercore_engagement', 'm')
      ->fields('m', ['destid1', 'destid2'])
      ->condition('m.sourceid1', $eid);
    return $query->execute()->fetchAll();
  }

}
