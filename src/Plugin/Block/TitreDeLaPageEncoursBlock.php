<?php

namespace Drupal\lesroidelareno\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a titre de la page encours block.
 *
 * @Block(
 *   id = "lesroidelareno_titre_de_la_page_encours",
 *   admin_label = @Translation("Titre de la page encours"),
 *   category = @Translation("Custom")
 * )
 */
class TitreDeLaPageEncoursBlock extends BlockBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'suffix_title' => ''
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['suffix_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('suffix title'),
      '#default_value' => $this->configuration['suffix_title']
    ];
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['suffix_title'] = $form_state->getValue('suffix_title');
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function build() {
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
    if (!empty($this->configuration['suffix_title']))
      $title = $title . ' ' . $this->configuration['suffix_title'];
    $build['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h1',
      '#value' => $title
    ];
    return $build;
  }
  
}
