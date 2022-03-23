<?php

namespace Drupal\lesroidelareno;

class lesroidelareno {
  
  static public function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }
  
}