<?php

namespace Drupal\lesroidelareno;

use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Url;

class lesroidelareno {
  
  static public function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }
  
  static public function getRouteVariations(Product $Product) {
    $link = '';
    /**
     *
     * @var \Drupal\Core\Render\Renderer $renderer
     */
    $renderer = \Drupal::service('renderer');
    if (!empty($Product->id()))
      $link = [
        '#type' => 'link',
        '#title' => t('Editer'),
        '#url' => Url::fromRoute("entity.commerce_product_variation.collection", [
          'commerce_product' => $Product->id()
        ]),
        '#options' => [
          'attributes' => [
            'target' => '_blank',
            'class' => []
          ]
        ]
      ];
    return $link;
  }
  
}