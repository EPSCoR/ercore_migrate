<?php

namespace Drupal\pt_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\pt_migrate\DefaultMetatag;

/**
 * Source plugin for highlight nodes.
 *
 * @MigrateSource(
 *   id = "highlight"
 * )
 */
class Highlight extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n');
    // Define the standard node fields to include.
    $query->fields('n', [
      'nid',
      'vid',
      'created',
      'changed',
      'title',
      'status',
      'promote',
      'uid',
    ]);

    // URL Alias.
    $query->addField('u', 'alias', 'url_alias');
    $query->leftJoin('url_alias', 'u',
      "u.source = CONCAT('node/', n.nid)"
    );

    // Body data.
    $query->addField('body', 'body_value', 'body');
    $query->leftJoin('field_data_body', 'body',
      "body.revision_id = n.vid"
    );

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields['title'] = $this->t('Title');
    $fields['id'] = $this->t('Node ID');
    $fields['uid'] = $this->t('Author ID');
    $fields['vid'] = $this->t('Node Revision ID');
    $fields['created'] = $this->t('Created Timestamp');
    $fields['changed'] = $this->t('Changed Timestamp');
    $fields['status'] = $this->t('Node Published Status');
    $fields['promote'] = $this->t('Promote on Homepage');
    $fields['url_alias'] = $this->t('URL Alias');
    return $fields;
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

}
