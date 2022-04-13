<?php

namespace Drupal\lesroidelareno;

use Drupal\Core\Form\FormStateInterface;
use Drupal\lesroidelareno\Services\FormDonneeSiteVar;
use Drupal\creation_site_virtuel\Entity\SiteTypeDatas;
use Stephane888\Debug\debugLog;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\lesroidelareno\Entity\DonneeSiteInternetEntity;
use function GuzzleHttp\json_encode;

class LesroidelarenoFormDonneeSite {
  
  static function getStepper() {
    return [
      'step1' => [
        'keys' => [
          'name'
        ]
      ],
      'step2' => [
        'keys' => [
          'type_site'
        ]
      ],
      'step3' => [
        'keys' => [
          'type_color_theme'
        ]
      ],
      'step3.1' => [
        'keys' => [
          'site-theme-color'
        ],
        "states" => [
          [
            "action" => "visible",
            "name" => "type_color_theme",
            "operator" => "==",
            "value" => "0",
            "state_name" => ""
          ]
        ]
      ],
      'step3.2' => [
        'keys' => [
          'color-primary',
          'color-link-hover',
          'color-secondary',
          'background'
        ],
        "states" => [
          [
            "action" => "visible",
            "name" => "type_color_theme",
            "operator" => "==",
            "value" => "1",
            "state_name" => ""
          ]
        ]
      ],
      'step4' => [
        'keys' => [
          'type_home_page'
        ]
      ],
      'step5' => [
        'keys' => [
          'pages'
        ]
      ],
      'step6' => [
        'keys' => [
          'contenus_transferer'
        ]
      ],
      'step7' => [
        'keys' => [
          'demande_traitement'
        ]
      ],
      'laststep' => []
    ];
  }
  
  static function getFieldForStep(array &$form, FormStateInterface $form_state) {
    $dsi_form = $form_state->get(FormDonneeSiteVar::$key_dsi_form);
    $element = $form_state->getTriggeringElement();
    // debugLog::kintDebugDrupal($element, 'getTriggeringElement', true);
    // on determine l'etape suivante si l'origin est le bouton next ou suivant.
    if ($form_state->has(FormDonneeSiteVar::$key_steps) && !empty($element['#name']) && $element['#name'] == 'op') {
      $steps = $form_state->get(FormDonneeSiteVar::$key_steps);
      $steppers = self::getStepper();
      /**
       *
       * @var DonneeSiteInternetEntity $entity
       */
      $entity = $form_state->get(FormDonneeSiteVar::$entity);
      // $nber_fields = count($steps);
      if ($form_state->get('step_direction') == '+') {
        // $values = $form_state->get(FormDonneeSiteVar::$fields_value);
        // $i = 0;
        foreach ($steppers as $k => $value) {
          if ($k == 'laststep') {
            $form_state->set(FormDonneeSiteVar::$laststep, true);
            $steps[$k] = [];
            break;
          }
          if (!empty($value['keys'])) {
            // Si un seul champs est deja present dans le formulaire d'étape, on passe à la suite.
            if (isset($steps[$k])) {
              continue;
            }
            // Application des conditions d'affichge
            if (!empty($value['states'])) {
              $valid = true;
              foreach ($value['states'] as $state) {
                $val = $entity->get($state['name'])->value;
                // \Drupal::messenger()->addStatus('form value => ' . json_encode($val) . ' :: state value =>' . $state['value'], true);
                if ($val !== $state['value'])
                  $valid = false;
              }
              if (!$valid)
                continue;
            }
            // \Drupal::messenger()->addStatus($k, true);
            $validStep = fasle;
            foreach ($value['keys'] as $fieldName) {
              if (!empty($dsi_form[$fieldName])) {
                // On reconstruit les options en function du choix du type de site.
                if ('type_home_page' == $fieldName) {
                  // On recupere la valeur du type_site;
                  $type_site = $entity->get('type_site')->target_id;
                  if (!empty($type_site)) {
                    $query = \Drupal::entityQuery("site_type_datas");
                    $query->condition("terms", [
                      $type_site
                    ], 'IN');
                    $ids = $query->execute();
                    // $theme1 = \Drupal\creation_site_virtuel\Entity\SiteInternetEntityType::load("theme1");
                    // dump($ids);
                    // dump($dsi_form[$fieldName]['widget']['#options']);
                    $nodes = SiteTypeDatas::loadMultiple($ids);
                    $options = [];
                    $view_site_type_datas = \Drupal::entityTypeManager()->getViewBuilder('site_type_datas');
                    foreach ($nodes as $node) {
                      $label = [
                        '#type' => 'html_tag',
                        '#tag' => 'div',
                        '#value' => $node->getName()
                      ];
                      $label += $view_site_type_datas->view($node, 'teaser');
                      $options[$node->id()] = $label;
                    }
                    $dsi_form[$fieldName]['widget']['#options'] = $options;
                  }
                  $form[$fieldName] = $dsi_form[$fieldName];
                  // debugLog::$max_depth = 7;
                  // $debug = [
                  // $dsi_form[$fieldName]['widget']["#options"],
                  // $form_state->getValue('type_site'),
                  // $form_state->get(FormDonneeSiteVar::$fields_value)
                  // ];
                  // debugLog::kintDebugDrupal($debug, 'field__' . $fieldName);
                }
                else
                  $form[$fieldName] = $dsi_form[$fieldName];
                // $steps[$k][$fieldName] = [];
                $validStep = true;
              }
            }
            if ($validStep) {
              $steps[$k] = [];
              break;
            }
          }
          //
          // if ($i == $nber_fields) {
          // // $form[$key] = $value;
          // // on definie le field
          // $steps[$key] = [];
          // $form_state->set(FormDonneeSiteVar::$key_steps, $steps);
          // break;
          // }
          // $i++;
        }
        $form_state->set(FormDonneeSiteVar::$key_steps, $steps);
      }
      elseif ($form_state->get('step_direction') == '-') {
        $key = array_key_last($steps);
        unset($steps[$key]);
        if (!empty($steps)) {
          $key = array_key_last($steps);
          $steppers = self::getStepper();
          if (!empty($steppers[$key]['keys'])) {
            foreach ($steppers[$key]['keys'] as $fieldName) {
              $form[$fieldName] = $dsi_form[$fieldName];
            }
          }
          // $form[$key] = $dsi_form[$key];
          $form_state->set(FormDonneeSiteVar::$key_steps, $steps);
          //
          $form_state->set(FormDonneeSiteVar::$laststep, false);
        }
      }
    }
    else {
      $steps = $form_state->get(FormDonneeSiteVar::$key_steps);
      /**
       * Car La derniere etape contenu dans steps est celle encours.
       *
       * @var string $key
       */
      $key = array_key_last($steps);
      $steppers = self::getStepper();
      if (!empty($steppers[$key])) {
        foreach ($steppers[$key]['keys'] as $fieldName) {
          $form[$fieldName] = $dsi_form[$fieldName];
        }
        // debugLog::kintDebugDrupal($dsi_form, 'getTriggeringElement_form', true);
      }
    }
  }
  
  /**
   *
   * @param array $form
   */
  static function getHeader($name, array &$form) {
    $form[$name] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'step-donneesite--header',
          'with-tablet',
          'mx-auto',
          'text-center'
        ]
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => 'Donnons vie à vos idées',
        '#attributes' => [
          'class' => [
            'step-donneesite--title'
          ]
        ]
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => " Repondez à quelques questions et obtenez les meilleurs outils pour vos créations ",
        '#attributes' => [
          'class' => [
            'step-donneesite--label'
          ]
        ]
      ]
    ];
  }
  
  /**
   *
   * @param array $form
   */
  static function getFooter($name, array &$form) {
    $form[$name] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'step-donneesite--header',
          'with-tablet',
          'mx-auto',
          'text-center'
        ]
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => 'Sauvegarde des données',
        '#attributes' => [
          'class' => [
            'step-donneesite--title'
          ]
        ]
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => " Enregistrer les données, et nous vous recontaterons dans 48 heures pour vous presenter votre nouveau site. ",
        '#attributes' => [
          'class' => [
            'step-donneesite--label'
          ]
        ]
      ]
    ];
  }
  
  static public function getListThemeColor() {
    return [
      'etincelle' => 'Etincelle',
      'chic' => 'Chic'
    ];
  }
  
  static public function getListPages() {
    return [
      'contact' => 'page : Contact ',
      'service' => 'page : Service ',
      'propos' => 'page  : à propos de nous ',
      'personnel' => 'page : personnel ',
      'tarif' => 'page : Nos tarifs ',
      'qui-sommes-nous' => 'page : Qui sommes nous '
    ];
  }
  
  static public function StatusTraitement() {
    return [
      'en_cours' => ' En cours ',
      'terminer' => ' Terminer '
    ];
  }
  
}