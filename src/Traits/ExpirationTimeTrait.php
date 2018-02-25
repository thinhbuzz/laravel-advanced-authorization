<?php


namespace Buzz\Authorization\Traits;
use Buzz\Authorization\Scopes\ExpirationTimeScope;

trait ExpirationTimeTrait
{

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ExpirationTimeScope());
    }
}