<?php

namespace Drupal\lesroidelareno;

class lesroidelareno {
  
  /**
   * Propriettaire et gerant de site web.
   *
   * @var string
   */
  private static $managerWebSite = 'gerant_de_site_web';
  
  /**
   * Propriettaire et gerant de ecommerce web.
   *
   * @var string
   */
  private static $managerEcommerce = 'manage_ecommerce';
  /**
   *
   * @var boolean
   */
  private static $isOwnerSite = NULL;
  
  /**
   *
   * @var boolean
   */
  private static $isAdministrator = NULL;
  
  /**
   *
   * @var boolean
   */
  private static $userIsAdministratorSite = NULL;
  
  /**
   *
   * @return array
   */
  static public function getCurrentUserId() {
    return \Drupal::currentUser()->id();
  }
  
  /**
   * Retoune les roles proprietaires de site web.
   *
   * @return string[]
   */
  static public function RolesHaveSites() {
    return [
      self::$managerWebSite => self::$managerWebSite,
      self::$managerEcommerce => self::$managerEcommerce
    ];
  }
  
  static public function isAdministrator() {
    if (self::$isAdministrator == NULL) {
      self::$isAdministrator = false;
      $roles = \Drupal::currentUser()->getRoles();
      if (in_array('administrator', $roles)) {
        self::$isAdministrator = true;
      }
    }
    return self::$isAdministrator;
  }
  
  /**
   * L'utilisateur connecté est proprietaire d'un site ou a les roles pour gerer
   * un site ? true:false;
   * On doit mettre le resultat en cache pour l'utilisateur et le domaine.
   * // on doit utiliser les caches pour cette information ?
   */
  static public function isOwnerSite() {
    if (self::$isOwnerSite === NULL) {
      self::$isOwnerSite = false;
      $roles = \Drupal::currentUser()->getRoles();
      foreach ($roles as $role) {
        if (!empty(self::RolesHaveSites()[$role])) {
          self::$isOwnerSite = true;
          break;
        }
      }
    }
    return self::$isOwnerSite;
  }
  
  /**
   * Permet de determiner si l'utilisateur connecter est administrateur du site.
   * (il faudra mettre en cache en fonction de la session et du domaine ).
   */
  static public function userIsAdministratorSite() {
    if (self::$userIsAdministratorSite === NULL) {
      /**
       *
       * @var \Drupal\domain_source\HttpKernel\DomainSourcePathProcessor $domain_source
       */
      $domain_source = \Drupal::service('domain_source.path_processor');
      $domain = $domain_source->getActiveDomain();
      if ($domain && self::isOwnerSite()) {
        $uid = self::getCurrentUserId();
        $user = \Drupal\user\Entity\User::load($uid);
        $domaines = $user->get('field_domain_admin')->getValue();
        foreach ($domaines as $value) {
          if ($value['target_id'] == $domain->id()) {
            self::$userIsAdministratorSite = false;
            break;
          }
        }
      }
      else
        self::$userIsAdministratorSite = false;
    }
    return self::$userIsAdministratorSite;
  }
  
}