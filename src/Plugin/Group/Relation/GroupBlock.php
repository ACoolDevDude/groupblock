<?php

namespace Drupal\groupblock\Plugin\Group\Relation;

use Drupal\group\Entity\GroupInterface;
use Drupal\group\Plugin\Group\Relation\GroupRelationBase;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a group relation type for blocks.
 *
 * @GroupRelationType(
 *   id = "group_block",
 *   label = @Translation("Group block"),
 *   description = @Translation("Adds block items to groups both publicly and privately."),
 *   entity_type_id = "block_content",
 *   entity_access = TRUE,
 *   reference_label = @Translation("Title"),
 *   reference_description = @Translation("The title of the block to add to the group"),
 *   deriver = "Drupal\groupblock\Plugin\Group\Relation\GroupBlockDeriver",
 *   handlers = {
 *     "access" = "Drupal\group\Entity\Access\GroupRelationshipTypeAccessControlHandler",
 *     "permission_provider" = "Drupal\groupblock\Plugin\RelationHandler\GroupBlockPermissionProvider",
 *   }
 * )
 */
class GroupBlock extends GroupRelationBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    $config['entity_cardinality'] = 1;
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    // Disable the entity cardinality field as the functionality of this module
    // relies on a cardinality of 1. We don't just hide it, though, to keep a UI
    // that's consistent with other group relations.
    $info = $this->t("This field has been disabled by the plugin to guarantee the functionality that's expected of it.");
    $form['entity_cardinality']['#disabled'] = TRUE;
    $form['entity_cardinality']['#description'] .= '<br /><em>' . $info . '</em>';

    return $form;
  }

  /**
   * Retrieves the block content type this plugin supports.
   *
   * @return \Drupal\block_content\Entity\BlockContentType
   *   The block content type this plugin supports.
   */
  protected function getBlockContentType() {
    return BlockContentType::load($this->getRelationType()->getEntityBundle());
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupOperations(GroupInterface $group) {
    $account = \Drupal::currentUser();
    $plugin_id = $this->getPluginId();
    $type = $this->getRelationType()->getEntityBundle();
    $operations = [];

    if ($group->hasPermission("create $plugin_id entity", $account)) {
      $route_params = ['group' => $group->id(), 'plugin_id' => $plugin_id];
      $operations["groupblock-create-$type"] = [
        'title' => $this->t('Create @type', ['@type' => \Drupal::entityTypeManager()->getDefinition($this->getRelationType()->getEntityTypeId())->getLabel()]),
        'url' => new Url('entity.group_content.create_form', $route_params),
        'weight' => 30,
      ];
    }

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    $dependencies['config'][] = 'block_content.type.' . $this->getRelationType()->getEntityBundle();
    return $dependencies;
  }

}
