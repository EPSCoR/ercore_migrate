<?php

namespace Drupal\ercore_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for the Users.
 *
 * @MigrateSource(
 *   id = "collaboration"
 * )
 */
class Collaboration extends SqlBase {

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
    $query->condition('n.type', 'er_collaboration');

    // Dates.
    $query->addField('date', 'field_er_collaboration_dates_value', 'date_start');
    $query->addField('date', 'field_er_collaboration_dates_value2', 'date_end');
    $query->leftJoin('field_data_field_er_collaboration_dates', 'date', "date.entity_id = n.nid");

    // Components.
    $query->addField('c', 'field_er_components_target_id', 'component');
    $query->leftJoin('field_data_field_er_components', 'c', "c.entity_id = n.nid AND c.bundle = :bundle",
      [':bundle' => 'er_collaboration']
    );

    // Body.
    $query->addField('b', 'body_value', 'body');
    $query->leftJoin('field_data_body', 'b', "b.entity_id = n.nid");

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
    $new_date = explode('T', $date);
    return $new_date[0];
  }

}
