<?php

namespace Drupal\ercore_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;

/**
 * Split single text field into multiple.
 *
 * Example:
 * @code
 * process:
 *   field_your_field_name:
 *       plugin: other_authors
 *       source: some_source_value
 * @endcode
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "other_authors"
 * )
 */
class OtherAuthors extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $separator = ',';
    if (strpos($value, '&') !== FALSE) {
      $separator = '&';
    }
    $values = explode($separator, $value);
    $return = [];
    foreach ($values as $value) {
      $return[] = trim($value);
    }
    return $return;
  }

}
