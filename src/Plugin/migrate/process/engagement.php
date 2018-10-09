<?php

namespace Drupal\ercore_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;

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
    drush_print($value);
    return $value;
  }

}
