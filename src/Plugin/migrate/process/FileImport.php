<?php

namespace Drupal\ercore_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;

/**
 * Import a file as a side-effect of a migration.
 *
 * @MigrateProcessPlugin(
 *   id = "file_import"
 * )
 */
class FileImport extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $file = NULL;
    $subpath = '';
    if (empty($value)) {
      // Skip this item if there's no URL.
      throw new MigrateSkipProcessException();
    }
    $values = explode('||', $value);
    $value = $values[1];
    $subpath = 'public://';
    if (!empty($values[0])) {
      $subpath = 'public://' . $values[0] . '/';
    }
    if (!empty($value)) {
      $old_file = self::getFileData($value);
      if (!empty($old_file[$value])) {
        $old_uri = str_replace('public://', 'public://migrate_files/', $old_file[$value]->uri);
        $new_uri = str_replace('public://', $subpath, $old_file[$value]->uri);
        $file = file_save_data($old_uri, $new_uri, FILE_EXISTS_REPLACE);
        $owner = User::load($old_file[$value]->uid);
        if (!empty($owner)) {
          $file->setOwner($owner);
        }
        $file->setFilename($old_file[$value]->filename);
        $file->setMimeType($old_file[$value]->filemime);
      }
    }
    return $file;
  }

  /**
   * Receives Node IDs, filters by collaboration status.
   *
   * @param int $fid
   *   File IDs.
   *
   * @return array
   *   Returns array of engagements.
   */
  public function getFileData($fid) {
    $query = Database::getConnection('default', 'migration')
      ->select('file_managed', 'f')
      ->fields('f', ['fid', 'uid', 'filename', 'filemime', 'uri', 'timestamp'])
      ->condition('f.fid', $fid);
    return $query->execute()->fetchAllAssoc('fid');
  }

}
