<?php

return [
    'blade_shortcut' => false,
    'super_user_key' => 'super_user',
    'groups' => [
        'super' => [
            'title' => 'Super User',
            'hidden' => true,
            'permissions' => [
                [
                    'title' => 'Super User',
                    'key' => 'super_user'
                ]
            ]
        ],
        'authorization' => [
            'title' => 'Authorization management',
            'hidden' => false,
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