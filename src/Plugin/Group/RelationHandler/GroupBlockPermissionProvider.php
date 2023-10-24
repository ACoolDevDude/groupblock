<?php

namespace Drupal\groupblock\Plugin\Group\RelationHandler;

use Drupal\group\Plugin\Group\RelationHandlerDefault\PermissionProvider;

/**
 * Provides group permissions for group_block GroupContent entities.
 */
class GroupBlockPermissionProvider extends PermissionProvider {

  /**
   * {@inheritdoc}
   */
  public function getEntityViewUnpublishedPermission($scope = 'any') {
    if ($scope === 'any') {
      // Backwards compatible permission name for 'any' scope.
      return "view unpublished $this->pluginId entity";
    }
    return parent::getEntityViewUnpublishedPermission($scope);
  }

}
