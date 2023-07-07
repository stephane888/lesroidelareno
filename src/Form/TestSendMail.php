<?php

namespace Drupal\lesroidelareno\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TestSendMail.
 */
class TestSendMail extends FormBase {
  
  /**
   * Drupal\Core\Mail\MailManagerInterface definition.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $pluginManagerMail;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->pluginManagerMail = $container->get('plugin.manager.mail');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'test_send_mail';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attributes']['class'][] = 'mx-auto';
    $form['#attributes']['class'][] = 'width-phone';
    $form['destinataire'] = [
      '#type' => 'textfield',
      '#title' => $this->t('destinataire'),
      '#maxlength' => 128,
      '#size' => 64,
      '#weight' => '0'
    ];
    $form['sujet'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sujet'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => 'test de mail',
      '#weight' => '0'
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit')
    ];
    
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format' ? $value['value'] : $value));
    }
  }
  
}
