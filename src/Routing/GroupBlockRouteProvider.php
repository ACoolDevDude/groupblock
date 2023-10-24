<?php

namespace Drupal\groupblock\Routing;

use Drupal\block_content\Entity\BlockContentType;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for group_block group content.
 */
class GroupBlockRouteProvider {

  /**
   * Provides the shared collection route for group block plugins.
   */
  public function getRoutes() {
    $routes = $plugin_ids = $permissions_add = $permissions_create = [];

    foreach (BlockContentType::loadMultiple() as $name => $block_bundle) {
      $plugin_id = "group_block:$name";

      $plugin_ids[] = $plugin_id;
      $permissions_add[] = "create $plugin_id content";
      $permissions_create[] = "create $plugin_id entity";
    }

    // If there are no media types yet, we cannot have any plugin IDs and should
    // therefore exit early because we cannot have any routes for them either.
    if (empty($plugin_ids)) {
      return $routes;
    }

    $routes['entity.group_content.group_block_relate_page'] = new Route('group/{group}/block/add');
    $routes['entity.group_content.group_block_relate_page']
      ->setDefaults([
        '_title' => 'Relate block',
        '_controller' => '\Drupal\groupblock\Controller\GroupBlockController::addPage',
      ])
      ->setRequirement('_group_permission', implode('+', $permissions_add))
      ->setRequirement('_group_installed_content', implode('+', $plugin_ids))
      ->setOption('_group_operation_route', TRUE);

    $routes['entity.group_content.group_block_add_page'] = new Route('group/{group}/block/create');
    $routes['entity.group_content.group_block_add_page']
      ->setDefaults([
        '_title' => 'Create block',
        '_controller' => '\Drupal\groupblock\Controller\GroupBlockController::addPage',
        'create_mode' => TRUE,
      ])
      ->setRequirement('_group_permission', implode('+', $permissions_create))
      ->setRequirement('_group_installed_content', implode('+', $plugin_ids))
      ->setOption('_group_operation_route', TRUE);

    return $routes;
  }

}
