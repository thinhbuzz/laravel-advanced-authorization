<?php


namespace Buzz\Authorization;


use Illuminate\Support\Facades\Facade;

class AuthorizationFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'authorization';
    }
}