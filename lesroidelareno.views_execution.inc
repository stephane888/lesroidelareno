<?php
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\wbumenudomain\WbumenudomainMenuItemDecorating;
use Drupal\views\ViewExecutable;

/**
 * implement hook_views_query_alter
 *
 * @param ViewExecutable $view
 * @param Drupal\views\Plugin\views\query\QueryPluginBase $query
 */
function lesroidelareno_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  
  if ($view->id() == 'produits_simialires') {
    
    _lesroidelareno_viewquery($query);
    // debugLog::$max_depth = 5;
    // debugLog::kintDebugDrupal($query->where,
    // 'lesroidelareno_views_query_alter__query');
    // foreach ($query->where as &$condition_group) {
    // if (isset($condition_group['type'])) {
    // $condition_group['type'] = 'OR';
    // }
    // }
    // orderBy non definit
    // $query->orderBy('order_field', 'ASC');
    // $query->addWhere('',
    // 'commerce_product__field_en_promotion_sur_les_roide.field_en_promotion_sur_les_roide_value',
    // 1, 'IN');
    //
    // $query->addField('commerce_product__4aa4f981fa',
    // 'field_en_promotion_sur_les_roide_value',
    // 'commerce_product__field_en_promotion_sur_les_roide');
    //
    // $query->queueTable('commerce_product__2b70704739');
    // $query->addTable('commerce_product__2b70704739');
    // //
    // $configuration = array(
    // 'type' => 'LEFT',
    // 'table' => 'taxonomy_index',
    // 'field' => 'nid',
    // 'left_table' => 'commerce_product_field_data',
    // 'left_field' => 'product_id',
    // 'operator' => '='
    // );
    // $join =
    // \Drupal\views\Views::pluginManager('join')->createInstance('standard',
    // $configuration);
    // $rel = $query->addRelationship('taxonomy_idx', $join, 'node_field_data');
    // $query->addTable('taxonomy_index', $rel, $join, 'taxonomy_idx');
    // $query->addWhere('', 'taxonomy_idx.tid', 2, 'IN');
  }
}

function _lesroidelareno_viewquery(QueryPluginBase &$query) {
  $configuration = array(
    'type' => 'LEFT',
    'table' => 'commerce_product__4aa4f981fa',
    'field' => 'entity_id',
    'left_table' => 'commerce_product_field_data',
    'left_field' => 'product_id',
    'operator' => '='
  );
  $join = \Drupal\views\Views::pluginManager('join')->createInstance('standard', $configuration);
  $rel = $query->addRelationship('kksa888', $join, 'node_field_data');
  // $query->addTable('commerce_product__4aa4f981fa', $rel, $join, 'kksa888');
  $activeDomain = WbumenudomainMenuItemDecorating::getCurrentActiveDomaineByUrl();
  if ('lesroisdelareno_fr' == $activeDomain || 'v2lesroisdelareno_kksa' == $activeDomain)
    $query->addWhere('grp1', 'kksa888.field_en_promotion_sur_les_roide_value', 1, '=');
  $query->addWhere('grp1', 'commerce_product_field_data.field_domain_access', WbumenudomainMenuItemDecorating::getCurrentActiveDomaineByUrl(), '=');
  $query->setWhereGroup('OR', 'grp1');
}
