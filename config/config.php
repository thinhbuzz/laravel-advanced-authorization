<?php

return [
    /*
     * Class name of models
     *
     * */
    'model_role' => \App\Role::class,
    'model_permission' => \App\Permission::class,
    'model_user' => \App\User::class,
    /*
     * Auto add Authorization to alias, if you want change or disable you can change in here.
     *
     * */
    'auto_alias' => true,
    'alias' => 'Authorization',
    /*
     * Add blade shortcut: @permission, @role, @anyRole, ...
     *
     * */
    'blade_shortcut' => true,
    /*
     * If you do not want to use the role level, you can switch to false and remove field level in migration.
     * */
    'user_level' => true,
    /*
     * Exception class name is used in middleware
     * */
    'role_exception' => \Buzz\Authorization\Exception\RoleDeniedException::class,
    'permission_exception' => \Buzz\Authorization\Exception\PermissionDeniedException::class,
    /*
     * level_exception will be required if option user_level is true and you use level middleware
     * */
    'level_exception' => \Buzz\Authorization\Exception\LevelDeniedException::class,
];