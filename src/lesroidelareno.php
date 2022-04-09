<?php

namespace Drupal\lesroidelareno;

class lesroidelareno {
  
  static public function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }
  
  static public function getListThemeColor() {
    return [
      'etincelle' => 'Etincelle',
      'chic' => 'Chic'
    ];
  }
  
  static public function getListPages() {
    return [
      'contact' => 'page Contact'
    ];
  }
  
}