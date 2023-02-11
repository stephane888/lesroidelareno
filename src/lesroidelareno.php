<?php

namespace Drupal\lesroidelareno;

use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\blockscontent\Entity\BlocksContents;

class lesroidelareno {
  
  /**
   *
   * @return array
   */
  static public function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }
  
  /**
   *
   * @param BlocksContents $BlocksContents
   */
  static public function manageBlocksContents(BlocksContents $BlocksContents) {
    $link = [
      '#type' => 'dropbutton',
      '#dropbutton_type' => 'small',
      '#links' => [
        'simple_form' => [
          'title' => t('Editer'),
          'url' => Url::fromRoute('entity.node.edit_form', [
            'node' => $BlocksContents->id()
          ]),
          '#options' => [
            'attributes' => [
              'target' => '_blank',
              'class' => []
            ]
          ]
        ],
        'demo' => [
          'title' => t('Traduction'),
          'url' => Url::fromRoute('entity.node.content_translation_overview', [
            'node' => $BlocksContents->id()
          ]),
          '#options' => [
            'attributes' => [
              'target' => '_blank',
              'class' => []
            ]
          ]
        ]
      ]
    ];
    return $link;
  }
  
  /**
   *
   * @param Node $node
   * @return string[]
   */
  static public function manageNode(Node $node) {
    $link = [
      '#type' => 'dropbutton',
      '#dropbutton_type' => 'small',
      '#links' => [
        'simple_form' => [
          'title' => t('Editer'),
          'url' => Url::fromRoute('entity.node.edit_form', [
            'node' => $node->id()
          ]),
          '#options' => [
            'attributes' => [
              'target' => '_blank',
              'class' => []
            ]
          ]
        ],
        'demo' => [
          'title' => t('Traduction'),
          'url' => Url::fromRoute('entity.node.content_translation_overview', [
            'node' => $node->id()
          ]),
          '#options' => [
            'attributes' => [
              'target' => '_blank',
              'class' => []
            ]
          ]
        ]
      ]
    ];
    return $link;
  }
  
  /**
   *
   * @param Product $Product
   * @return string|string[]|\Drupal\Core\Url[]|string[][][]|array[][][]
   */
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