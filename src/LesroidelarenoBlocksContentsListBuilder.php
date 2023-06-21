<?php

namespace Drupal\lesroidelareno;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

class LesroidelarenoBlocksContentsListBuilder extends EntityListBuilder {
  protected $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
  protected $field_source = \Drupal\domain_source\DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD;
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Blocks contents ID');
    $header['type'] = 'Type';
    $header['name'] = $this->t('Name');
    $header[$this->field_access] = 'field_access';
    $header[$this->field_source] = 'field_source';
    return $header + parent::buildHeader();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\blockscontent\Entity\BlocksContents $entity */
    $row['id'] = $entity->id();
    $row['type'] = $entity->bundle();
    $row['name'] = Link::createFromRoute($entity->label(), 'entity.blocks_contents.edit_form', [
      'blocks_contents' => $entity->id()
    ]);
    $row[$this->field_access] = $entity->get($this->field_access)->target_id;
    $row[$this->field_source] = $entity->get($this->field_source)->target_id;
    return $row + parent::buildRow($entity);
  }
  
}