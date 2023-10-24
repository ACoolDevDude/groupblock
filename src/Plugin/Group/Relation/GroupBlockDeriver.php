<?php

namespace Drupal\groupblock\Plugin\Group\Relation;

use Drupal\block_content\Entity\BlockContentType;
use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\group\Plugin\Group\Relation\GroupRelationTypeInterface;

class GroupBlockDeriver extends DeriverBase {

  /**
   * {@inheritdoc}.
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    assert($base_plugin_definition instanceof GroupRelationTypeInterface);
    $this->derivatives = [];

    foreach (BlockContentType::loadMultiple() as $name => $block_type) {
      $label = $block_type->label();

      $this->derivatives[$name] = clone $base_plugin_definition;
      $this->derivatives[$name]->set('entity_bundle', $name);
      $this->derivatives[$name]->set('label', t('Group block (@type)', ['@type' => $label]));
      $this->derivatives[$name]->set('description', t('Adds %type content to groups both publicly and privately.', ['%type' => $label]));
    }

    return $this->derivatives;
  }

}
