<?php

namespace Drupal\ercore_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\MigrateIdMapInterface;

/**
 * Source plugin for the Users.
 *
 * @MigrateSource(
 *   id = "collaborator"
 * )
 */
class Collaborator extends SqlBase {

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
    $query->condition('n.type', 'er_collaborators');

    // Department.
    $query->addField('v', 'field_er_collab_department_unit_value', 'department');
    $query->leftJoin('field_data_field_er_collab_department_unit', 'v', "v.entity_id = n.nid");

    // Name.
    $query->addField('c', 'field_er_collab_name_value', 'name');
    $query->leftJoin('field_data_field_er_collab_name', 'c', "c.entity_id = n.nid");

    // Type / Status.
    $query->addField('t', 'field_er_collab_user_status_value', 'type');
    $query->leftJoin('field_data_field_er_collab_user_status', 't', "t.entity_id = n.nid");

    // Institution.
    $query->addField('i', 'field_er_collab_inst_ref_target_id', 'institution');
    $query->leftJoin('field_data_field_er_collab_inst_ref', 'i', "i.entity_id = n.nid");

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
    if ($row->getSourceProperty('type') === 'Participant') {
      $this->idMap
        ->saveIdMapping($row, array(), MigrateIdMapInterface::STATUS_IGNORED);
      $this->currentRow = NULL;
      $this->currentSourceIds = NULL;
    }
    return parent::prepareRow($row);
  }

}
