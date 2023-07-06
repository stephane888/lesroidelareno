<?php

namespace Drupal\lesroidelareno\HandlerClass;

use Drupal\block_content\BlockContentAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class BlockContentAccess extends BlockContentAccessControlHandler {
  
  // use AccessDefault;
  /**
   * Le but principale ici est d'eleminer toutes les entites de type
   * blockcontent.
   * On pourrait etre informer par message et avoir une liste.
   *
   * {@inheritdoc}
   * @see \Drupal\blockscontent\BlocksContentsAccessControlHandler::checkAccess()
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    return parent::checkAccess($entity, $operation, $account);
  }
  
}