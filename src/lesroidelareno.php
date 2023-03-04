<?php

namespace Drupal\lesroidelareno;

class lesroidelareno {
  
  /**
   *
   * @return array
   */
  static public function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }
  
}