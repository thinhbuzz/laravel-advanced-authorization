<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConfigTest extends TestCase
{
    public function testConfig()
    {
        $this->assertTrue(is_array(config('authorization')));
        $this->assertTrue(is_array(config('authorization.groups')));
        $this->assertTrue(is_array(config('authorization.groupKeys')));
        $this->assertTrue(count(config('authorization.groupKeys')) === count(config('authorization.groups')));
        $this->assertTrue(is_bool(config('authorization.blade_shortcut')));
    }
}