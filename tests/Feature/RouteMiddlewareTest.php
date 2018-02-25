<?php

namespace Tests\Feature;

class RouteMiddlewareTest extends AuthorizationFeatureTestCase
{
    public function testCanMiddlewareWithAcceptUser()
    {
        /**
         * @var \App\Models\Role[] $roles
         * @var \App\User $user
         */
        list($user, $roles) = $this->createUserAndAttach();
        /**
         * @var \App\Models\PermissionRole $permission
         */
        $permission = collect($roles)->random()->permissions->random();

        $this->createRouter($this->getTestUri(__METHOD__), ['permission:' . $permission->permission]);
        $response = $this->actingAs($user)
            ->get($this->getTestUri(__METHOD__));
        $response->assertStatus(200);
    }

    public function testCanMiddlewareWithDeniedUser()
    {
        /**
         * @var \App\Models\Role[] $roles
         * @var \App\User $user
         */
        list($user, $roles) = $this->createUserAndAttach();
        /**
         * @var string $permission
         */
        $permission = $this->getDeniedPermission($roles);

        $this->createRouter($this->getTestUri(__METHOD__), ['permission:' . $permission]);
        $response = $this->actingAs($user)
            ->get($this->getTestUri(__METHOD__));
        $response->assertStatus(500);
    }

    public function testCanMiddlewareWithDeniedUserAndMultiPermission()
    {
        /**
         * @var \App\Models\Role[] $roles
         * @var \App\User $user
         */
        list($user, $roles) = $this->createUserAndAttach();
        /**
         * @var \App\Models\PermissionRole $permission
         */
        $permission = collect($roles)->random()->permissions->random();
        /**
         * @var string $permissionDenied
         */
        $permissionDenied = $this->getDeniedPermission($roles);
        $middleware = ['permission:' . join('&', [$permission, $permissionDenied])];

        $this->createRouter($this->getTestUri(__METHOD__), $middleware);
        $response = $this->actingAs($user)
            ->get($this->getTestUri(__METHOD__));
        $response->assertStatus(500);
    }

    public function testCanAnyMiddlewareWithAcceptUser()
    {
        /**
         * @var \App\Models\Role[] $roles
         * @var \App\User $user
         */
        list($user, $roles) = $this->createUserAndAttach();
        /**
         * @var \App\Models\PermissionRole $permission
         */
        $permission = collect($roles)->random()->permissions->random();
        /**
         * @var string $permissionDenied
         */
        $permissionDenied = $this->getDeniedPermission($roles);
        $middleware = ['permission:' . join('|', [$permission->permission, $permissionDenied])];

        $this->createRouter($this->getTestUri(__METHOD__), $middleware);
        $response = $this->actingAs($user)
            ->get($this->getTestUri(__METHOD__));
        $response->assertStatus(200);
    }
}