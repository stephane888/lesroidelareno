<?php

namespace Drupal\lesroidelareno\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Checkboxes;

/**
 * Handles matching of current domain.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("lesroidelareno_tag_filter")
 */
class lesroidelarenoTag extends InOperator {
  
  /**
   *
   * @var array Stores all operations which are available on the form.
   */
  protected $valueOptions = NULL;
  
  /**
   * Liste d'option pour le second champs.
   *
   * @var array
   */
  protected $secondOptions = [];
  
  /**
   *
   * @var string
   */
  protected $value2 = null;
  
  /**
   * Definit le label;
   *
   * {@inheritdoc}
   *
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    //
  }
  
  public function defaultExposeOptions() {
    parent::defaultExposeOptions();
    // $this->options['expose']['reduce'] = FALSE;
  }
  
  protected function defineOptions() {
    $options = parent::defineOptions();
    
    $options['operator']['default'] = 'in';
    $options['value']['default'] = [];
    $options['expose']['contains']['reduce'] = [
      'default' => FALSE
    ];
    // put select vocabulary hier.
    
    return $options;
  }
  
  /**
   * This kind of construct makes it relatively easy for a child class
   * to add or remove functionality by overriding this function and
   * adding/removing items from this array.
   */
  public function operators() {
    $operators = [
      'in' => [
        'title' => $this->t('Is one of'),
        'short' => $this->t('in'),
        'short_single' => $this->t('='),
        'method' => 'opSimple',
        'values' => 1
      ],
      'not in' => [
        'title' => $this->t('Is not one of'),
        'short' => $this->t('not in'),
        'short_single' => $this->t('<>'),
        'method' => 'opSimple',
        'values' => 1
      ]
    ];
    // if the definition allows for the empty operator, add it.
    if (!empty($this->definition['allow empty'])) {
      $operators += [
        'empty' => [
          'title' => $this->t('Is empty (NULL)'),
          'method' => 'opEmpty',
          'short' => $this->t('empty'),
          'values' => 0
        ],
        'not empty' => [
          'title' => $this->t('Is not empty (NOT NULL)'),
          'method' => 'opEmpty',
          'short' => $this->t('not empty'),
          'values' => 0
        ]
      ];
    }
    //
    return $operators;
  }
  
  /**
   * Build strings from the operators() for 'select' options.
   */
  public function operatorOptions($which = 'title') {
    $options = [];
    foreach ($this->operators() as $id => $info) {
      $options[$id] = $info[$which];
    }
    
    return $options;
  }
  
  protected function operatorValues($values = 1) {
    $options = [];
    foreach ($this->operators() as $id => $info) {
      if (isset($info['values']) && $info['values'] == $values) {
        $options[] = $id;
      }
    }
    
    return $options;
  }
  
  private function getTerms($target_id = 0, $vid = "typesite") {
    $database = \Drupal::database();
    $query = $database->select('taxonomy_term_field_data', 't');
    $query->join('taxonomy_term__parent', 'p', 't.tid = p.entity_id');
    $query->condition('p.parent_target_id', $target_id);
    $query->condition('t.vid', $vid);
    $query->orderBy('t.name');
    $query->addField('t', 'tid');
    $query->addField('t', 'name');
    return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
  }
  
  /**
   * Child classes should be used to override this function and set the
   * 'value options', unless 'options callback' is defined as a valid function
   * or static public method to generate these values.
   *
   * This can use a guard to be used to reduce database hits as much as
   * possible.
   *
   * @return array|null The stored values from $this->valueOptions.
   */
  public function getValueOptions() {
    if (!empty($this->valueOptions)) {
      return $this->valueOptions;
    }
    // $tree =
    // \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('typesite',
    // 0, 1, TRUE);
    // $result = [];
    // foreach ($tree as $term) {
    // $result[$term->id()] = $term->get('name')->value;
    // }
    // dump($result);
    $resultQuery = $this->getTerms();
    $ids = null;
    foreach ($resultQuery as $row) {
      $ids[$row['tid']] = $row['name'];
    }
    // $result = [];
    // if ($ids) {
    // $tree =
    // \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($ids);
    // foreach ($tree as $term) {
    // $result[$term->id()] = $term->get('name')->value;
    // }
    // }
    // $this->valueOptions = $result;
    //
    $this->valueOptions = $ids;
    
    return $this->valueOptions;
  }
  
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [];
    $options = [];
    
    $exposed = $form_state->get('exposed');
    if (!$exposed) {
      // Add a select all option to the value form.
      $options = [
        'all' => $this->t('Select all')
      ];
    }
    
    $this->getValueOptions();
    $options += $this->valueOptions;
    $default_value = (array) $this->value;
    
    $which = 'all';
    if (!empty($form['operator'])) {
      $source = ':input[name="options[operator]"]';
    }
    if ($exposed) {
      $identifier = $this->options['expose']['identifier'];
      
      if (empty($this->options['expose']['use_operator']) || empty($this->options['expose']['operator_id'])) {
        // exposed and locked.
        $which = in_array($this->operator, $this->operatorValues(1)) ? 'value' : 'none';
      }
      else {
        $source = ':input[name="' . $this->options['expose']['operator_id'] . '"]';
      }
      
      if (!empty($this->options['expose']['reduce'])) {
        $options = $this->reduceValueOptions();
        
        if (!empty($this->options['expose']['multiple']) && empty($this->options['expose']['required'])) {
          $default_value = [];
        }
      }
      
      if (empty($this->options['expose']['multiple'])) {
        if (empty($this->options['expose']['required']) && (empty($default_value) || !empty($this->options['expose']['reduce'])) || isset($this->options['value']['all'])) {
          $default_value = 'All';
        }
        elseif (empty($default_value)) {
          $keys = array_keys($options);
          $default_value = array_shift($keys);
        }
        else {
          $copy = $default_value;
          $default_value = array_shift($copy);
        }
      }
    }
    
    if ($which == 'all' || $which == 'value') {
      $form['value'] = [
        '#type' => $this->valueFormType,
        '#title' => $this->valueTitle,
        '#options' => $options,
        '#default_value' => $default_value,
        // These are only valid for 'select' type, but do no harm to checkboxes.
        '#multiple' => TRUE,
        // The value options can be a multidimensional array if the value form
        // type is a select list, so make sure that they are counted correctly.
        '#size' => min(count($options, COUNT_RECURSIVE), 8)
      ];
      $user_input = $form_state->getUserInput();
      if ($exposed && !isset($user_input[$identifier])) {
        $user_input[$identifier] = $default_value;
        $form_state->setUserInput($user_input);
      }
      
      if ($which == 'all') {
        if (!$exposed && (in_array($this->valueFormType, [
          'checkbox',
          'checkboxes',
          'radios',
          'select'
        ]))) {
          $form['value']['#prefix'] = '<div id="edit-options-value-wrapper">';
          $form['value']['#suffix'] = '</div>';
        }
        // Setup #states for all operators with one value.
        foreach ($this->operatorValues(1) as $operator) {
          $form['value']['#states']['visible'][] = [
            $source => [
              'value' => $operator
            ]
          ];
        }
      }
    }
    $inputs = \Drupal::request()->query->all();
    $optionSecond = [];
    if (!empty($inputs['filter_theme_model'])) {
      $this->secondOptions = [];
      foreach ($this->getTerms($inputs['filter_theme_model']) as $row) {
        $this->secondOptions[$row['tid']] = $row['name'];
      }
      if (!empty($this->secondOptions)) {
        $optionSecond = [
          'all' => $this->t('Select all')
        ];
        $optionSecond += $this->secondOptions;
      }
    }
    else {
      $this->secondOptions = [];
    }
    
    \Stephane888\Debug\debugLog::kintDebugDrupal([
      'second' => $this->secondOptions,
      'default' => $default_value,
      'val' => $this->value,
      'form_state' => $form_state->getValues(),
      'form' => $form,
      'query' => \Drupal::request()->query->all(),
      'post' => \Drupal::request()->request->all()
    ], "buildForm_valueForm", true);
    
    $form['sous_menu'] = [
      '#type' => 'select',
      '#title' => 'Selectionner une sous categorie',
      '#options' => $optionSecond,
      '#weight' => 20,
      '#access' => empty($this->secondOptions) ? false : true
    ];
  }
  
  protected function opSimple() {
    if (empty($this->value)) {
      return;
    }
    $this->ensureMyTable();
    $terms = $this->value;
    foreach ($this->value as $tid) {
      foreach ($this->getTerms($tid) as $row) {
        $terms[] = $row['tid'];
      }
    }
    // Get second value;
    if ($this->value2) {
      $terms = [
        $this->value2
      ];
    }
    // \Stephane888\Debug\debugLog::kintDebugDrupal($this->value, 'opSimple',
    // true);
    //
    // We use array_values() because the checkboxes keep keys and that can cause
    // array addition problems.
    $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", array_values($terms), $this->operator);
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
  
  /**
   * Determines if the input from a filter should change the generated query.
   *
   * @param array $input
   *        The exposed data for this view.
   *        
   * @return bool TRUE if the input for this filter should be included in the
   *         view query.
   *         FALSE otherwise.
   */
  public function acceptExposedInput($input) {
    if (empty($this->options['exposed'])) {
      return TRUE;
    }
    if (!empty($input['sous_menu']) && $input['sous_menu'] != 'all') {
      $this->value2 = $input['sous_menu'];
    }
    if (!empty($this->options['expose']['use_operator']) && !empty($this->options['expose']['operator_id']) && isset($input[$this->options['expose']['operator_id']])) {
      $this->operator = $input[$this->options['expose']['operator_id']];
    }
    
    if (!empty($this->options['expose']['identifier'])) {
      if ($this->options['is_grouped']) {
        $value = $input[$this->options['group_info']['identifier']];
      }
      else {
        $value = $input[$this->options['expose']['identifier']];
      }
      
      // Various ways to check for the absence of non-required input.
      if (empty($this->options['expose']['required'])) {
        if (($this->operator == 'empty' || $this->operator == 'not empty') && $value === '') {
          $value = ' ';
        }
        
        if ($this->operator != 'empty' && $this->operator != 'not empty') {
          if ($value == 'All' || $value === []) {
            return FALSE;
          }
          
          // If checkboxes are used to render this filter, do not include the
          // filter if no options are checked.
          if (is_array($value) && Checkboxes::detectEmptyCheckboxes($value)) {
            return FALSE;
          }
        }
        
        if (!empty($this->alwaysMultiple) && $value === '') {
          return FALSE;
        }
      }
      if (isset($value)) {
        $this->value = $value;
        if (empty($this->alwaysMultiple) && empty($this->options['expose']['multiple']) && !is_array($value)) {
          $this->value = [
            $value
          ];
        }
      }
      else {
        return FALSE;
      }
    }
    
    return TRUE;
  }
  
}

