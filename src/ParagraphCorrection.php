<?php

namespace Drupal\lesroidelareno;

/**
 * Ce fichier permet de mettre sur pied un enssemble d'outil permettant de
 * corriger de maniere rapide les erreurs liées au module paragraph.
 *
 * @author stephane
 *
 */
class ParagraphCorrection {

  /**
   * Recupere les entitées qui utilise un type de paragraph qui a été
   * supprimé.
   */
  static function getEntityUnInstall($paragraph_type_id, $id) {
    $entities = [
      'block_content',
      'node'
    ];

    /**
     * --
     */
    foreach ($entities as $entity_type_id) {
      $table = $entity_type_id . '_layout_paragraphs';
      /**
       *
       * @var \Drupal\Core\Database\Connection $dateBase
       */
      $dateBase = \Drupal::database();
      if ($dateBase->schema()->tableExists($table)) {
        $query = $dateBase->select($table, 'n');
        $query->condition('bundle', $paragraph_type_id);
        $results = $query->execute();
        dd($results);
      }
    }
  }

}