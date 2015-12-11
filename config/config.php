<?php

return [
    'model_role' => \App\Role::class,
    'model_permission' => \App\Permission::class,
    'model_user' => \App\User::class,
    'auto_alias' => true,
    'alias' => 'Authorization',
    'eager_type' => 1,
    'blade_shortcut' => true
];