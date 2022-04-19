<?php

namespace Drupal\lesroidelareno;

use Drupal\Core\Form\FormStateInterface;
use Drupal\lesroidelareno\Services\FormDonneeSiteVar;
use Drupal\creation_site_virtuel\Entity\SiteTypeDatas;
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
          'site_theme_color'
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
          'color_primary',
          'color_secondary',
          'color_linkhover',
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
      // 'login' => [],
      'laststep' => []
    ];
  }
  
  static function getStepper2() {
    return [
      'step1' => [
        'keys' => [
          'name'
        ]
      ],
      // 'step2' => [
      // 'keys' => [
      // 'type_site'
      // ]
      // ],
      'step3' => [
        'keys' => [
          'type_color_theme'
        ]
      ],
      'step3.1' => [
        'keys' => [
          'site_theme_color'
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
          'color_primary',
          'color_secondary',
          'color_linkhover',
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
      // 'login' => [],
      'laststep' => []
    ];
  }
  
  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  static function getFieldForStep(array &$form, FormStateInterface $form_state, $model = 1) {
    $dsi_form = $form_state->get(FormDonneeSiteVar::$key_dsi_form);
    $element = $form_state->getTriggeringElement();
    // debugLog::kintDebugDrupal($element, 'getTriggeringElement', true);
    // on determine l'etape suivante si l'origin est le bouton next ou suivant.
    if ($form_state->has(FormDonneeSiteVar::$key_steps) && !empty($element['#name']) && $element['#name'] == 'op') {
      $steps = $form_state->get(FormDonneeSiteVar::$key_steps);
      if ($model == 1)
        $steppers = self::getStepper();
      else
        $steppers = self::getStepper2();
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
          if ($k == 'login') {
            if (\Drupal::currentUser()->id()) {
              continue;
            }
            else {
              $steps[$k] = [];
              break;
            }
          }
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
            // Application des conditions d'affichge.
            if (!empty($value['states'])) {
              $valid = true;
              foreach ($value['states'] as $state) {
                if ($entity->hasField($state['name']))
                  $first = $entity->get($state['name'])->first();
                if ($first) {
                  $val = $first->getValue();
                  $val = reset($val);
                  // dump($val, $k );
                }
                // \Drupal::messenger()->addStatus('form value => ' . json_encode($val) . ' :: state value =>' . $state['value'], true);
                if (isset($val) && $val !== $state['value'])
                  $valid = false;
              }
              if (!$valid)
                continue;
            }
            // \Drupal::messenger()->addStatus($k . ' value :: ' . json_encode($validStep), true);
            $validStep = false;
            foreach ($value['keys'] as $fieldName) {
              if (!empty($dsi_form[$fieldName])) {
                // On reconstruit les options en function du choix du type de site ou on recupere l'id du formulaire dans l'url (site-type-datas-id).
                if ('type_home_page' == $fieldName) {
                  // On recupere la valeur du type_site;
                  $type_site = $entity->get('type_site')->target_id;
                  // on recupere la valeur de : site-type-datas-id
                  $request = \Drupal::request();
                  $id_type_site = $request->query->get('site-type-datas-id');
                  if ($id_type_site) {
                    /**
                     *
                     * @var DonneeSiteInternetEntity $entity
                     */
                    $entity->setTypeHomePage($id_type_site);
                    break;
                  }
                  elseif (!empty($type_site)) {
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
                  else {
                    $query = \Drupal::entityQuery("site_type_datas");
                    $ids = $query->execute();
                    $options = [];
                    $view_site_type_datas = \Drupal::entityTypeManager()->getViewBuilder('site_type_datas');
                    $nodes = SiteTypeDatas::loadMultiple($ids);
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
                }
                // on met à jour la liste, en ajoutant les images.
                elseif ('type_color_theme' == $fieldName) {
                  if (!empty($dsi_form[$fieldName]['widget']['#options'])) {
                    foreach ($dsi_form[$fieldName]['widget']['#options'] as $key => $value) {
                      if ($key == 0) {
                        $file = \Drupal\file\Entity\File::load(1576);
                      }
                      else {
                        $file = \Drupal\file\Entity\File::load(1577);
                      }
                      
                      $dsi_form[$fieldName]['widget']['#options'][$key] = [
                        '#type' => 'html_tag',
                        '#tag' => 'div',
                        [
                          '#theme' => 'image_style',
                          '#style_name' => 'medium',
                          '#uri' => ($file) ? $file->getFileUri() : ''
                        ],
                        [
                          '#type' => 'html_tag',
                          '#tag' => 'div',
                          '#attributes' => [
                            'class' => 'mt-5'
                          ],
                          '#value' => $value
                        ]
                      ];
                    }
                  }
                  $form[$fieldName] = $dsi_form[$fieldName];
                }
                elseif ('site_theme_color' == $fieldName || 'pages' == $fieldName) {
                  if (!empty($dsi_form[$fieldName]['widget']['#options'])) {
                    if ('site_theme_color' == $fieldName)
                      $optionsDefault = self::getListThemeColorCallBack();
                    else
                      $optionsDefault = self::getListPagesCallback();
                    foreach ($dsi_form[$fieldName]['widget']['#options'] as $key => $value) {
                      if (!empty($optionsDefault[$key]['image'])) {
                        $file = \Drupal\file\Entity\File::load($optionsDefault[$key]['image']);
                        $dsi_form[$fieldName]['widget']['#options'][$key] = [
                          '#type' => 'html_tag',
                          '#tag' => 'div',
                          [
                            '#theme' => 'image_style',
                            '#style_name' => 'medium',
                            '#uri' => ($file) ? $file->getFileUri() : ''
                          ],
                          [
                            '#type' => 'html_tag',
                            '#tag' => 'div',
                            '#attributes' => [
                              'class' => [
                                'mt-5'
                              ]
                            ],
                            '#value' => $value
                          ],
                          [
                            '#type' => 'html_tag',
                            '#tag' => 'div',
                            '#attributes' => [
                              'class' => [
                                'mt-5',
                                'text-hover'
                              ]
                            ],
                            '#value' => $optionsDefault[$key]['description']
                          ]
                        ];
                      }
                    }
                  }
                  $form[$fieldName] = $dsi_form[$fieldName];
                }
                else {
                  $form[$fieldName] = $dsi_form[$fieldName];
                  // dump($form);
                }
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
      if (!empty($steps)) {
        $key = array_key_last($steps);
        $steppers = self::getStepper();
        if (!empty($steppers[$key])) {
          foreach ($steppers[$key]['keys'] as $fieldName) {
            $form[$fieldName] = $dsi_form[$fieldName];
          }
        }
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
    $lists = [];
    foreach (self::getListThemeColorCallBack() as $key => $value) {
      $lists[$key] = $value['titre'];
    }
    return $lists;
  }
  
  static public function getListThemeColorCallBack() {
    return [
      'audacieux' => [
        'titre' => 'Audacieux',
        'description' => 'Un esprist aventurier, fort et fier',
        'image' => 1578
      ],
      'black' => [
        'titre' => 'Jet black',
        'description' => 'Un ton sérieux et saisissant, avec une touche de résilience ',
        'image' => 1579
      ],
      'etincele' => [
        'titre' => 'Etincelant',
        'description' => "Vif et inspiré avec une lueur d'enthousiasme",
        'image' => 1580
      ],
      'precieux' => [
        'titre' => 'Précieux',
        'description' => "Doux et câlin, comme un délicat mélange de joies",
        'image' => 1581
      ],
      'retro' => [
        'titre' => 'Retro',
        'description' => "Une impression vintage, telle une antiquité qui a été restaurée",
        'image' => 1582
      ],
      'inspire' => [
        'titre' => 'Nature',
        'description' => "Influent et stimulant, avec un charisme accueillant",
        'image' => 1578
      ]
    ];
  }
  
  static public function getListPages() {
    $lists = [];
    foreach (self::getListPagesCallback() as $key => $value) {
      $lists[$key] = $value['titre'];
    }
    return $lists;
  }
  
  static public function getListPagesCallback() {
    return [
      'contact' => [
        'titre' => 'Contactez nous ',
        'description' => '',
        'image' => ''
      ],
      'service' => [
        'titre' => 'Services ',
        'description' => '',
        'image' => ''
      ],
      'propos' => [
        'titre' => 'A propos de nous',
        'description' => '',
        'image' => ''
      ],
      'personnel' => [
        'titre' => 'personnel ',
        'description' => '',
        'image' => ''
      ],
      'tarif' => [
        'titre' => 'Nos tarifs ',
        'description' => '',
        'image' => ''
      ],
      'realisations' => [
        'titre' => 'Realisations ',
        'description' => '',
        'image' => ''
      ],
      'qui-sommes-nous' => [
        'titre' => 'Qui sommes nous ',
        'description' => '',
        'image' => ''
      ],
      'blog' => [
        'titre' => 'Blog ',
        'description' => '',
        'image' => ''
      ]
    ];
  }
  
  static public function StatusTraitement() {
    return [
      'en_cours' => ' En cours ',
      'terminer' => ' Terminer '
    ];
  }
  
}