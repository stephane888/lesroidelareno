<?php

namespace Drupal\lesroidelareno\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;

/**
 * Class DonneeSiteInternetEntityController.
 *
 * Returns responses for Donnee site internet des utilisateurs routes.
 */
class LesroidelarenoConfigController extends ControllerBase implements ContainerInjectionInterface {
  
  function ConfigPage() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Page config'
    ];
  }
  
  function ConfigPage2() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Page config 2'
    ];
  }
  
}