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
    if (!empty($Product->id())) {
      $nbre = count($Product->getVariationIds());
      $error = false;
      if ($nbre)
        $nbre = 'Editer ( ' . $nbre . ' variations )';
      else {
        $nbre = 'Ajouter une variation ( ne serra pas affichage, ni dupliqué )';
        $error = true;
      }
      $link = [
        '#type' => 'link',
        '#title' => $nbre,
        '#url' => Url::fromRoute("entity.commerce_product_variation.collection", [
          'commerce_product' => $Product->id()
        ]),
        '#options' => [
          'attributes' => [
            'target' => '_blank',
            'class' => [],
            'style' => $error ? 'color:#f00;' : '',
            'title' => $error ? ' Vous devez ajouter au moins une variation pour que votre produit soit valide ' : ' Votre produit est valide '
          ]
        ]
      ];
    }
    
    return $link;
  }
  
}