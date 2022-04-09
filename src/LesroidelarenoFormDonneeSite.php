<?php

namespace Drupal\lesroidelareno;

use Drupal\Core\Form\FormStateInterface;
use Drupal\lesroidelareno\Services\FormDonneeSiteVar;
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
      'step5' => [
        'keys' => [
          'pages'
        ]
      ],
      'laststep' => []
    ];
  }
  
  static function getFieldForStep(array &$form, FormStateInterface $form_state) {
    $dsi_form = $form_state->get(FormDonneeSiteVar::$key_dsi_form);
    // on determine l'etape suivante.
    if ($form_state->has(FormDonneeSiteVar::$key_steps)) {
      $steps = $form_state->get(FormDonneeSiteVar::$key_steps);
      $steppers = self::getStepper();
      // $nber_fields = count($steps);
      if ($form_state->get('step_direction') == '+') {
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
                $val = $form_state->getValue($state['name']);
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
  
}