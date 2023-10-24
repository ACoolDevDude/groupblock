# Group Block

This module is designed to associate group specific block content
with a group when using the [Group](https://www.drupal.org/project/group) module.

## Requirements
 - Group module (http://drupal.org/project/group) 1.2+.

## Migration

There is no easy way to migrate from [this patch](https://www.drupal.org/project/group/issues/3014720) to this module as everyone's group content IDs will be different but the following code will hopefully point you in the right direction.

```
$old_basic_block = 'group_content_type_480bfd4b64e96';
$new_basic_block = 'group_content_type_cd92bde0bdc52';
$group_content_storage = Drupal::entityTypeManager()->getStorage('group_content');

$group_block_old_basic = $group_content_storage->loadByProperties([
  'type' => $old_basic_block
]);

foreach ($group_block_old_basic as $groupblock) {
  $new_group_content = $group_content_storage->create([
    'type' => $new_basic_block,
    'langcode' => 'en',
    'gid' => $groupblock->gid->target_id,
    'entity_id' => $groupblock->entity_id->target_id
  ]);
  $new_group_content->save();
  $groupblock->delete();
}
```
