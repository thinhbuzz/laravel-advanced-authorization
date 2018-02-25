<?php

namespace App\Models;

use Buzz\Authorization\Events\RebuildPermissionRoleEvent;
use Buzz\Authorization\Traits\ExpirationTimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $permission
 * @property int $role_id
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PermissionRole extends Model
{

    use ExpirationTimeTrait;

    public $table = 'permissions_roles';
    protected $fillable = ['permission'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dispatchesEvents = [
        'deleted' => RebuildPermissionRoleEvent::class,
        'saved' => RebuildPermissionRoleEvent::class,
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id', 'role_id');
    }

    /**
     * @param Builder $builder
     * @param array $permissions
     * @return Builder
     */
    public function scopeGetRoleByPermissions(Builder $builder, array $permissions)
    {
        return $builder->with(['role'])->whereIn('permission', $permissions);
    }
}
