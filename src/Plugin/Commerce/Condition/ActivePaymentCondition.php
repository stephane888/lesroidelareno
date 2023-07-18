<?php

namespace Drupal\lesroidelareno\Plugin\Commerce\Condition;

use Drupal\commerce\Plugin\Commerce\Condition\ConditionBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\domain\DomainNegotiatorInterface;

/**
 * Provides a basic condition for orders.
 *
 * @CommerceCondition(
 *   id = "lesroisreno_active_payment_condition",
 *   label = @Translation("Permet aux clients d'activer/desactiver cette methode "),
 *   entity_type = "commerce_order",
 * )
 */
class ActivePaymentCondition extends ConditionBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }
  
  /**
   * Permet d'activer le paiement si l'utilisateu la definie.
   * Ce filtre n'est pas adapter à notre logique.
   * On a besoin de determiner la paymentMethod sur laquelle on filtre les
   * données.( Ce qui n'est pas possible ).
   *
   * {@inheritdoc}
   */
  public function evaluate(EntityInterface $order) {
    // $this->assertEntity($order);
    // /** @var \Drupal\commerce_order\Entity\Order $order */
    // throw new \Exception("ddf");
    // /**
    // *
    // * @var DomainNegotiatorInterface $negotiator
    // */
    // $negotiator = \Drupal::service('domain.negotiator');
    // $datas =
    // \Drupal::entityTypeManager()->getStorage('commerce_payment_config')->loadByProperties([
    // 'domain_id' => $negotiator->getActiveId()
    // ]);
    // if ($datas) {
    // /** @var \Drupal\lesroidelareno\Entity\CommercePaymentConfig
    // $commerce_payment_config */
    // $commerce_payment_config = reset($datas);
    // return $commerce_payment_config->PaymentMethodIsActive();
    // }
    return true;
  }
  
}