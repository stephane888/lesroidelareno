<?php

namespace Drupal\lesroidelareno\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MailTestSendFulloptions.
 */
class MailTestSendFulloptions extends ConfigFormBase {
  
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
  protected function getEditableConfigNames() {
    return [
      'lesroidelareno.mailtestsendfulloptions'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mail_test_send_fulloptions';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('lesroidelareno.mailtestsendfulloptions');
    
    $form['destinataire'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Destinataire'),
      '#default_value' => $config->get('destinataire'),
      '#description' => $this->t('Votre email')
    ];
    $form['sujet'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sujet'),
      '#description' => $this->t('test d&#039;envoit de mail'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('sujet')
    ];
    $form['message_brute'] = [
      '#type' => 'textarea',
      '#title' => $this->t('message brute'),
      '#description' => $this->t('Contenu du message brute'),
      '#default_value' => $config->get('message_brute')
    ];
    $form['message_html'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Message html'),
      '#default_value' => $config->get('message_html') && $config->get('message_html')['value'] ? $config->get('message_html')['value'] : '',
      '#format' => $config->get('message_html') && $config->get('message_html')['format'] ? $config->get('message_html')['format'] : 'full_html'
    ];
    $form['attachements'] = [
      '#type' => 'file',
      '#title' => $this->t('attachements'),
      '#default_value' => $config->get('attachements')
    ];
    $form['mails_en_bc'] = [
      '#type' => 'textarea',
      '#title' => $this->t('mails en BC'),
      '#description' => $this->t('List de mail en BC separer par une virule'),
      '#default_value' => $config->get('mails_en_bc')
    ];
    $form['mails_en_cc'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mails en CC'),
      '#description' => $this->t('List de mails en CC'),
      '#default_value' => $config->get('mails_en_cc')
    ];
    return parent::buildForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('lesroidelareno.mailtestsendfulloptions');
    $config->set('test', $form_state->getValue('test'));
    $config->set('destinataire', $form_state->getValue('destinataire'));
    $config->set('sujet', $form_state->getValue('sujet'));
    $config->set('message_brute', $form_state->getValue('message_brute'));
    $config->set('message_html', $form_state->getValue('message_html'));
    $config->set('attachements', $form_state->getValue('attachements'));
    $config->set('mails_en_bc', $form_state->getValue('mails_en_bc'));
    $config->set('mails_en_cc', $form_state->getValue('mails_en_cc'));
    $config->save();
    // send mails.
    $key = 'login_rx_vuejs_send_mail';
    $to = $form_state->getValue('destinataire');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $params['message'] = $form_state->getValue('message_brute');
    $params['message'] .= $form_state->getValue('message_html')['value'];
    $params['subject'] = $form_state->getValue('sujet');
    $this->pluginManagerMail->mail('lesroidelareno', $key, $to, $langcode, $params);
  }
  
}
