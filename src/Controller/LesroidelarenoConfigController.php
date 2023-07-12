<?php

namespace Drupal\lesroidelareno\Controller;

use Drupal\Core\Controller\ControllerBase;
use Stephane888\Debug\Repositories\ConfigDrupal;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DonneeSiteInternetEntityController.
 *
 * Returns responses for Donnee site internet des utilisateurs routes.
 */
class LesroidelarenoConfigController extends ControllerBase {
  
  /**
   * Permet de configurer les prises de RDV.
   *
   * @return array
   */
  public function UpdateDefaultConfigsCreneauRdv() {
    $content = ConfigDrupal::config('prise_rendez_vous.default_configs');
    $entity = RdvConfigEntity::load($content['id']);
    if (!$entity) {
      $this->messenger()->addStatus('new RDV config is create', true);
      $entity = RdvConfigEntity::create();
      $entity->set('id', $content['id']);
      $entity->set('label', $content['label']);
      $entity->set('jours', \Drupal\prise_rendez_vous\PriseRendezVousInterface::jours);
      $entity->save();
    }
    // On cree le formulaire pour la configuration de base des prises de
    // rendez-vous.
    $form = $this->entityFormBuilder()->getForm($entity);
    
    return $form;
  }
  
  // function ConfigPage() {
  // return [
  // '#type' => 'html_tag',
  // '#tag' => 'div',
  // '#value' => 'Page config'
  // ];
  // }
  
  // function ConfigPage2() {
  // return [
  // '#type' => 'html_tag',
  // '#tag' => 'div',
  // '#value' => 'Page config 2'
  // ];
  // }
}