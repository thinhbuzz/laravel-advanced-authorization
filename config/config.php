<?php

return [
    /*
     * Class name of models
     *
     * */
    'model' => [
        'role' => \App\Role::class,
        'permission' => \App\Permission::class,
        'user' => \App\User::class,
    ],
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
    'exception' => [
        'role' => \Buzz\Authorization\Exception\RoleDeniedException::class,
        'permission' => \Buzz\Authorization\Exception\PermissionDeniedException::class,
        /*
         * level will be required if option user_level is true and you use level middleware
         * */
        'level' => \Buzz\Authorization\Exception\LevelDeniedException::class,

    ],
    'cache' => [
        'enable' => true,
        'event' => true, // enable listen event for forget and update cache
        'time' => 43829, //a month
        'auto_update' => false,//false: forget cache, true: forget and put new cache
    ]
];