<?php

return [
    'model_role' => \App\Role::class,
    'model_permission' => \App\Permission::class,
    'model_user' => \App\User::class,
    'auto_alias' => true,
    'alias' => 'Authorization',
    'blade_shortcut' => true,
    'user_level' => true,
    'role_exception' => \Buzz\Authorization\Exception\RoleDeniedException::class,
    'permission_exception' => \Buzz\Authorization\Exception\PermissionDeniedException::class,
    'level_exception' => \Buzz\Authorization\Exception\LevelDeniedException::class,
];