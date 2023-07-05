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
  private static $isOwnerSite = null;
  
  /**
   *
   * @var boolean
   */
  private static $isAdministrator = null;
  
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
   * L'utilisateur connecté est proprietaire de site ? true:false;
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
  
}