<?php

namespace Drupal\lesroidelareno\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\BooleanOperator;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;

/**
 * Handles matching of current domain.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("lesroidelareno_domain_filter")
 */
class lesroidelarenoDomainFilter extends BooleanOperator {
  
  /**
   * Definit le label;
   *
   * {@inheritdoc}
   *
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->value_value = t(' Available on current domain ');
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function operators() {
    $options = parent::operators();
    return $options;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $real_field = $this->tableAlias . '.' . $this->realField;
    /** @var \Drupal\domain\DomainNegotiatorInterface $domain_negotiator */
    $domain_negotiator = \Drupal::service('domain.negotiator');
    $current_domain = $domain_negotiator->getActiveDomain();
    $current_domain_id = $current_domain->id();
    //
    if (!empty($this->value)) {
      $this->query->addWhere('OR', $real_field, $current_domain_id, '=');
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'url.site';
    return $contexts;
  }
  
}