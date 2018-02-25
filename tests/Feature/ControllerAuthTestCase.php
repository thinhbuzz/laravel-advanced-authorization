<?php

namespace Tests\Feature;

class ControllerAuthTestCase extends AuthorizationFeatureTestCase
{
    public function testControllerCheckByAuthGuest()
    {
        $this->createRouter(
            $this->getTestUri(__METHOD__),
            [],
            $this->getAuthCallable(collect(config('authorization.groupKeys.authorization'))->random())
        );

        $response = $this->get($this->getTestUri(__METHOD__));
        var_dump($response->baseResponse);
        $response->assertStatus(500);
    }

    protected function getAuthCallable(string $permission = '')
    {
        return function (\Auth $auth) use ($permission) {
            $permissionException = config('authorization.exception.permission_denied');
            var_dump($auth->check());
            if (!$auth->check()) {
                throw new $permissionException();
            }
            if (!$auth->user()->can([$permission])) {
                throw new $permissionException();
            }
            return response([]);
        };
    }

    public function testControllerCheckByAuthUser()
    {
        /**
         * @var \App\Models\Role[] $roles
         * @var \App\User $user
         */
        list($user, $roles) = $this->createUserAndAttach();
        /**
         * @var string $permissionDenied
         */
        $permissionDenied = $this->getDeniedPermission($roles);
        $this->createRouter(
            $this->getTestUri(__METHOD__),
            [],
            $this->getAuthCallable($permissionDenied)
        );

        $response = $this->actingAs($user)->get($this->getTestUri(__METHOD__));
        $response->assertStatus(500);
    }
}