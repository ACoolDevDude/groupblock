<?php

namespace Drupal\groupblock\Plugin\GroupContentEnabler;

use Drupal\block_content\Entity\BlockContentType;
use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Class GroupBlockDeriver
 *
 * @package Drupal\groupblock\Plugin\GroupContentEnabler
 */
class GroupBlockDeriver extends DeriverBase {

  /**
   * {@inheritdoc}.
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach (BlockContentType::loadMultiple() as $name => $block_type) {
      $label = $block_type->label();

      $this->derivatives[$name] = [
        'entity_bundle' => $name,
        'label' => t('Group block (@type)', ['@type' => $label]),
        'description' => t('Adds %type content to groups both publicly and privately.', ['%type' => $label]),
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
