<?php

namespace Drupal\lesroidelareno\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\SortArray;
use Drupal\lesroidelareno\LesroidelarenoFormDonneeSite;
use Drupal\lesroidelareno\Services\FormDonneeSiteVar;
use Stephane888\Debug\debugLog;

/**
 * Form controller for Donnee site internet des utilisateurs edit forms.
 *
 * @ingroup lesroidelareno
 */
class DonneeSiteInternetEntityForm extends ContentEntityForm {
  
  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // $fieldTem = $this->entity->getFieldDefinitions()['type_site'];
    // dump($fieldTem->getSetting('handler_settings'));
    /* @var \Drupal\lesroidelareno\Entity\DonneeSiteInternetEntity $entity */
    /**
     * On sauvegarde les champs qui vont etre utiliser durant les etapes.
     */
    if (!$form_state->has(FormDonneeSiteVar::$key_dsi_form)) {
      $dsi_form = [];
      // on retire les elements qui ne correspondent pas à un champs.
      foreach (parent::buildForm($form, $form_state) as $key => $field) {
        if (!empty($field['#type']) && !empty($field['widget'])) {
          $dsi_form[$key] = $field;
        }
      }
      // On reordonne les champs par ordre de poids.
      uasort($dsi_form, [
        SortArray::class,
        'sortByWeightProperty'
      ]);
      $form_state->set(FormDonneeSiteVar::$key_dsi_form, $dsi_form);
      // dump($dsi_form);
    }
    
    // dump($form_state->getStorage());
    $form['donnee-internet-entity'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'donnee-internet-entity-next-field',
        'class' => [
          'step-donneesite',
          'mx-auto',
          'text-center'
        ]
      ]
    ];
    if ($form_state->has(FormDonneeSiteVar::$key_steps)) {
      // $this->messenger()->addStatus(FormDonneeSiteVar::$key_steps, true);
      LesroidelarenoFormDonneeSite::getFieldForStep($form['donnee-internet-entity'], $form_state);
    }
    else
      LesroidelarenoFormDonneeSite::getHeader('ctm_description', $form['donnee-internet-entity']);
    
    //
    $form['donnee-internet-entity']['container_buttons'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'd-flex',
          'justify-content-around',
          'align-items-center',
          'step-donneesite--submit'
        ]
      ],
      '#weight' => 45
    ];
    
    if ($form_state->has(FormDonneeSiteVar::$key_steps) && count($form_state->get(FormDonneeSiteVar::$key_steps)) > 1) {
      $form['donnee-internet-entity']['container_buttons']['previews'] = [
        '#type' => 'submit',
        '#value' => 'Precedent',
        '#button_type' => 'secondary',
        '#submit' => [
          [
            $this,
            'selectPreviewsFieldSubmit'
          ]
        ],
        '#ajax' => [
          'callback' => '::selectPreviewsFieldSCallback',
          'wrapper' => 'donnee-internet-entity-next-field',
          'effect' => 'fade'
        ],
        '#attributes' => [
          'class' => [
            'd-inline-block',
            'w-auto',
            'btn btn-secondary'
          ]
        ]
      ];
    }
    if (!$form_state->get(FormDonneeSiteVar::$laststep)) {
      $form['donnee-internet-entity']['container_buttons']['next'] = [
        '#type' => 'submit',
        '#value' => 'Suivant',
        '#button_type' => 'secondary',
        '#submit' => [
          [
            $this,
            'selectNextFieldSubmit'
          ]
        ],
        '#ajax' => [
          'callback' => '::selectNextFieldSCallback',
          'wrapper' => 'donnee-internet-entity-next-field',
          'effect' => 'fade'
        ],
        '#attributes' => [
          'class' => [
            'd-inline-block',
            'w-auto'
          ]
        ]
      ];
    }
    // save datas
    else {
      $form['donnee-internet-entity']['container_buttons']['submit'] = [
        '#type' => 'submit',
        '#value' => 'Enregistre les données',
        '#button_type' => 'secondary',
        '#submit' => [
          [
            $this,
            'saveSubmit'
          ]
        ],
        '#ajax' => [
          'callback' => '::selectNextFieldSCallback',
          'wrapper' => 'donnee-internet-entity-next-field',
          'effect' => 'fade'
        ],
        '#attributes' => [
          'class' => [
            'd-inline-block',
            'w-auto'
          ]
        ]
      ];
    }
    
    //
    // if (!$this->entity->isNew()) {
    // $form['new_revision'] = [
    // '#type' => 'checkbox',
    // '#title' => $this->t('Create new revision'),
    // '#default_value' => FALSE,
    // '#weight' => 10
    // ];
    // }
    // $this->messenger()->addMessage('buildForm ffff', 'status', true);
    return $form;
  }
  
  public function selectNextFieldSCallback(array $form, FormStateInterface $form_state) {
    return $form['donnee-internet-entity'];
  }
  
  public function selectPreviewsFieldSCallback(array $form, FormStateInterface $form_state) {
    return $form['donnee-internet-entity'];
  }
  
  public function selectNextFieldSubmit($form, FormStateInterface $form_state) {
    // if (!empty($form['donnee-internet-entity']['name']))
    // $this->messenger()->addStatus('selectNextFieldSubmit :: ' . json_encode($form_state->getValue('name')), true);
    // on determine l'etape.
    if ($form_state->has(FormDonneeSiteVar::$key_steps)) {
      $values = $form_state->getUserInput();
      $steps = $form_state->get(FormDonneeSiteVar::$key_steps);
      $fieldsValue = $form_state->get(FormDonneeSiteVar::$fields_value);
      $dsi_form = $form_state->get(FormDonneeSiteVar::$key_dsi_form);
      $steppers = LesroidelarenoFormDonneeSite::getStepper();
      $k_last = array_key_last($steps);
      if (!empty($steppers[$k_last]['keys']))
        foreach ($steppers[$k_last]['keys'] as $fieldName) {
          if (isset($values[$fieldName])) {
            // $this->messenger()->addStatus($fieldName . ' :: ' . json_encode($values));
            $fieldsValue[$fieldName] = $values[$fieldName];
            if (!empty($form['donnee-internet-entity'][$fieldName])) {
              // dump($form['donnee-internet-entity'][$fieldName]);
              // debugLog::$max_depth = 7;
              // debugLog::kintDebugDrupal($form['donnee-internet-entity'][$fieldName], $fieldName, true);
              $dsi_form[$fieldName] = $form['donnee-internet-entity'][$fieldName];
              /**
               * Il faut nettoyer le #value, car si cette valeur est remplie on ne pourra plus mettre à jour.
               * De plus, on doit mettre la valeur encours dans default value, pour mettre de voir la precedente si l'utilisateur revient en arriere.
               */
              if (isset($dsi_form[$fieldName]['widget'][0]['value']['#value'])) {
                $dsi_form[$fieldName]['widget'][0]['value']['#default_value'] = $dsi_form[$fieldName]['widget'][0]['value']['#value'];
                unset($dsi_form[$fieldName]['widget'][0]['value']['#value']);
              }
              elseif (isset($dsi_form[$fieldName]['widget'][0]['color']['#value'])) {
                $dsi_form[$fieldName]['widget'][0]['color']['#default_value'] = $dsi_form[$fieldName]['widget'][0]['color']['#value'];
                unset($dsi_form[$fieldName]['widget'][0]['color']['#value']);
              }
              // elseif (isset($dsi_form[$fieldName]['widget'][0]['target_id']['#value'])) {
              // $dsi_form[$fieldName]['widget'][0]['target_id']['#default_value'] = $dsi_form[$fieldName]['widget'][0]['target_id']['#value'];
              // unset($dsi_form[$fieldName]['widget'][0]['target_id']['#value']);
              // }
              elseif (isset($dsi_form[$fieldName]['widget']['value']['#value'])) {
                $dsi_form[$fieldName]['widget']['value']['#default_value'] = $dsi_form[$fieldName]['widget']['value']['#value'];
                unset($dsi_form[$fieldName]['widget']['value']['#value']);
              }
              else {
                // debugLog::$max_depth = 7;
                // debugLog::kintDebugDrupal($form['donnee-internet-entity'][$fieldName], $fieldName, true);
              }
            }
            else {
              // dump('error :: ' . $fieldName);
            }
          }
        }
      /**
       * Permet de mettre à jour le champs.
       * ( Permet de concerver la valeur selectionner par l'utilisateur ).
       */
      $form_state->set(FormDonneeSiteVar::$key_dsi_form, $dsi_form);
      //
      // $form_state->set(FormDonneeSiteVar::$key_steps, $steps);
      //
      $form_state->set(FormDonneeSiteVar::$fields_value, $fieldsValue);
    }
    else {
      $form_state->set(FormDonneeSiteVar::$key_steps, []);
    }
    $form_state->set('step_direction', '+');
    $form_state->setRebuild(true);
  }
  
  public function selectPreviewsFieldSubmit($form, FormStateInterface $form_state) {
    $form_state->set('step_direction', '-');
    // $this->messenger()->addStatus('selectPreviewsFieldSubmit :: ' . json_encode($form_state->getValues()), true);
    $form_state->setRebuild(true);
  }
  
  public function saveSubmit($form, FormStateInterface $form_state) {
    $fieldsValue = $form_state->get(FormDonneeSiteVar::$fields_value);
    dump($fieldsValue);
  }
  
  /**
   * La validation se ferra à la fin.
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Entity\ContentEntityForm::validateForm()
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // $entity = parent::validateForm($form, $form_state);
    // return $entity;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->messenger()->addMessage('save entity', true);
    $entity = $this->entity;
    
    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();
      
      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime($this->time->getRequestTime());
      $entity->setRevisionUserId($this->account->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }
    
    $status = parent::save($form, $form_state);
    
    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Donnee site internet des utilisateurs.', [
          '%label' => $entity->label()
        ]));
        break;
      
      default:
        $this->messenger()->addMessage($this->t('Saved the %label Donnee site internet des utilisateurs.', [
          '%label' => $entity->label()
        ]));
    }
    $form_state->setRedirect('entity.donnee_internet_entity.canonical', [
      'donnee_internet_entity' => $entity->id()
    ]);
  }
  
}
