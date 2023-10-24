<?php

namespace Drupal\groupblock\Plugin\Entity;

use Drupal\group\Entity\GroupRelationshipType;

/**
 * Provides a content enabler for block items.
 *
 * @GroupConfigEnabler(
 *   id = "group_block_type",
 *   label = @Translation("Group block type"),
 *   description = @Translation("Adds block type to groups both publicly and privately."),
 *   entity_type_id = "block_type",
 * )
 */
 class GroupBlockType extends GroupRelationshipType {}
