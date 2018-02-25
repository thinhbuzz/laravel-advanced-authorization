<?php

namespace Tests\Unit;

use App\User;
use App\Models\Role;
use Faker\Factory;
use Tests\TestCase;

class RoleTestCase extends TestCase
{

    public $faker;

    /**
     * RoleManagementTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    /**
     * @return Role
     */
    protected function createRole()
    {
        $role = new Role();
        $role->name = $this->faker->name;
        $this->assertNotTrue(is_int($role->id));
        $this->assertTrue($role->save());
        $this->assertTrue(is_int($role->id));
        return $role;
    }

    /**
     * @return User
     */
    protected function createUser()
    {
        return User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ]);
    }
}