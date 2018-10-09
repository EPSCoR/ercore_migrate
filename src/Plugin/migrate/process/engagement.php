<?php

namespace Drupal\ercore_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\Core\Database\Database;

/**
 * Date processing, changes time code from D7 date.
 *
 * Example:
 * @code
 * process:
 *   field_ercore_ev_engagement:
 *     plugin: engagement
 *     source: nid
 * @endcode
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "engagement"
 * )
 */
class Engagement extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return self::getId($value);
  }

  /**
   * Receives Node Id, returns Node ID of referencing Engagement.
   *
   * @param int $nid
   *   Receives Node ID of original node.
   *
   * @return int
   *   Returns integer ID of related node.
   */
  public function getId($nid) {
    $query = Database::getConnection('default', 'migration')
      ->select('field_data_field_er_cal_entity_reference', 'e')
      ->fields('e', ['field_er_cal_entity_reference_target_id'])
      ->condition('e.entity_id', $nid);
    return $query->execute()->fetchField();
  }

}
