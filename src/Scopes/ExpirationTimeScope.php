<?php


namespace Buzz\Authorization\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Carbon;

class ExpirationTimeScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $now = Carbon::now(config('app.timezone'));
        $builder->where(function (Builder $query) use ($now) {
            $query->orWhereNull('start_time')
                ->orWhere('start_time', '<=', $now);
        })
            ->where(function (Builder $query) use ($now) {
                $query->orWhereNull('end_time')
                    ->orWhere('end_time', '>=', $now);
            });

    }
}