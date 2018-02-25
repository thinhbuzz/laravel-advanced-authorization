<?php
$config = [
    'blade_shortcut' => false,
    'groups' => [
        'authorization' => [
            'title' => 'Authorization management',
            'permissions' => [
                [
                    'title' => 'Create role',
                    'key' => 'create_role'
                ],
                [
                    'title' => 'Update role',
                    'key' => 'update_role'
                ],
                [
                    'title' => 'delete role',
                    'key' => 'delete_role'
                ],
            ]
        ]
    ],
    'exception' => [
        'permission_denied' => \Buzz\Authorization\Exception\PermissionDeniedException::class
    ],
    'groupKeys' => []
];

$config['groupKeys'] = array_map(function ($group) {
    return array_map(function ($permission) {
        return $permission['key'];
    }, $group['permissions']);
}, $config['groups']);

return $config;