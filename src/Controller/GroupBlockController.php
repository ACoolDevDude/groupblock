<?php

namespace Drupal\groupblock\Controller;

use Drupal\group\Entity\Controller\GroupContentController;
use Drupal\group\Entity\GroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for 'group_block' GroupContent routes.
 */
class GroupBlockController extends GroupContentController {

  /**
   * The group content plugin manager.
   *
   * @var \Drupal\group\Plugin\GroupContentEnablerManagerInterface
   */
  protected $pluginManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->pluginManager = $container->get('plugin.manager.group_content_enabler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function addPage(GroupInterface $group, $create_mode = FALSE) {
    $build = parent::addPage($group, $create_mode);

    // Do not interfere with redirects.
    if (!is_array($build)) {
      return $build;
    }

    // Overwrite the label and description for all of the displayed bundles.
    $storage_handler = $this->entityTypeManager->getStorage('block_type');
    foreach ($this->addPageBundles($group, $create_mode) as $plugin_id => $bundle_name) {
      if (!empty($build['#bundles'][$bundle_name])) {
        $plugin = $group->getGroupType()->getContentPlugin($plugin_id);
        $bundle_label = $storage_handler->load($plugin->getEntityBundle())->label();

        $t_args = ['%block_type' => $bundle_label];
        $description = $create_mode
          ? $this->t('Create a block of type %block_type in the group.', $t_args)
          : $this->t('Add an existing block of type %block_type to the group.', $t_args);

        $build['#bundles'][$bundle_name]['label'] = $bundle_label;
        $build['#bundles'][$bundle_name]['description'] = $description;
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function addPageBundles(GroupInterface $group, $create_mode) {
    $bundles = [];

    // Retrieve all group_media plugins for the group's type.
    $plugin_ids = $this->pluginManager->getInstalledIds($group->getGroupType());
    foreach ($plugin_ids as $key => $plugin_id) {
      if (strpos($plugin_id, 'group_block:') !== 0) {
        unset($plugin_ids[$key]);
      }
    }

    // Retrieve all of the responsible group content types, keyed by plugin ID.
    $storage = $this->entityTypeManager->getStorage('group_content_type');
    $properties = ['group_type' => $group->bundle(), 'content_plugin' => $plugin_ids];
    foreach ($storage->loadByProperties($properties) as $bundle => $group_content_type) {
      /** @var \Drupal\group\Entity\GroupContentTypeInterface $group_content_type */
      $bundles[$group_content_type->getContentPluginId()] = $bundle;
    }

    return $bundles;
  }

}
