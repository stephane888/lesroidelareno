<?php

namespace Drupal\lesroidelareno\HandlerClass;

use Drupal\webform\WebformEntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\lesroidelareno\lesroidelareno;

/**
 * Le webform est à revoir par example les conditions de delete et aussi les
 * autres $operation.
 *
 * @author stephane
 *        
 */
class WebformAccess extends WebformEntityAccessControlHandler {
  
  /**
   * on herite pas accessDefault car c'est publick pour
   * WebformEntityAccessControlHandler;
   *
   * {@inheritdoc}
   * @see \Drupal\blockscontent\BlocksContentsAccessControlHandler::checkAccess()
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $isOwnerSite = lesroidelareno::isOwnerSite();
    $isAdministrator = lesroidelareno::isAdministrator();
    // dump($entity->getEntityTypeId() . ' :: ' . $operation);
    // if ($operation == 'update' || $operation == 'delete') {
    // $debug = [
    // 'entity_uid' => $entity->getOwnerId(),
    // 'current_user_uid' => lesroidelareno::getCurrentUserId(),
    // 'entity' => $entity->toArray()
    // ];
    // \Stephane888\Debug\debugLog::$max_depth = 5;
    // \Stephane888\Debug\debugLog::kintDebugDrupal($debug,
    // $entity->getEntityTypeId() . '---checkAccess---' . $operation . '--',
    // true);
    // }
    switch ($operation) {
      // Tout le monde peut voir les contenus publiées.
      case 'view':
        if ($entity->isPublished()) {
          return AccessResult::allowed();
        }
        elseif ($isAdministrator)
          return AccessResult::allowed();
      // On met à jour si l'utilisateur est autheur ou s'il est administrateur.
      case 'update':
      case 'delete':
        if ($isAdministrator)
          return AccessResult::allowed();
        elseif ($isOwnerSite && $entity->getOwnerId() == lesroidelareno::getCurrentUserId()) {
          return AccessResult::allowed();
        }
    }
    // on bloque au cas contraire.
    return AccessResult::forbidden("Wb-Horizon, Vous n'avez pas les droits pour effectuer cette action");
  }
  
}