# Advanced authorization in Laravel 5.*

> This package inspired by [zizaco/entrust](https://github.com/Zizaco/entrust) and [bican/roles](https://github.com/romanbican/roles).

- [Installation](#installation)
    - [Composer](#composer)
    - [Provider](#provider)
    - [Alias](#alias)
    - [Middleware](#middleware)
- [Configuration](#configuration)
    - [Pubslish](#pubslish)
    - [Config package](#config-package)
    - [Config model](#config-model)
- [Instruction](#instruction)
    - [Creating data](#creating-data)
    - [Attach/ detach role and permission](#attach-detach-data)
    - [Checking role/ permission/ level](#checking-role-permission-level)
    - [Use Middleware](#check-with-middleware)

## Installation
### Composer

Run command:
  
```
composer require buzz/laravel-advanced-authorization 1.*
```
    
Or open composer.json, insert into bellow code and run command ``composer update`` 
    
```
"buzz/laravel-advanced-authorization": "1.*",
``` 

### Provider

Insert two providers to ``providers`` (``config/app.php``)

```php
\Buzz\Authorization\AuthorizationServiceProvider::class,
\Buzz\Authorization\UtilitieServiceProvider::class,
```
``UtilitieServiceProvider`` not required, this provider only help you publish models and seed of package by using the command. After publish you can remove it.

### Alias

By default, the package will automatically add ``Authorization`` to ``aliases``, you can edit in the config of the package.

### Middleware

Open ``app/Http/Kernel.php`` and insert into bellow code to routeMiddleware:

```php
'role' => \Buzz\Authorization\Middleware\RoleMiddleware::class,
'permission' => \Buzz\Authorization\Middleware\PermissionMiddleware::class,
'level' => \Buzz\Authorization\Middleware\LevelMiddleware::class,
```

## Configuration
- So publish config, migration you can run command

```
php artisan vendor:publish --provider="\Buzz\Authorization\UtilitieServiceProvider" --tag=config
php artisan vendor:publish --provider="\Buzz\Authorization\UtilitieServiceProvider" --tag=migrations
```

And after that run command: ``php artisan migrate`` (maybe you want edit migration file before run this command)

- So publish seed, model you can run command: ``php artisan authorization:seeder`` and ``php artisan authorization:model``
To perform these commands, ``UtilitieServiceProvider`` added to ``providers`` (``config/app.php``) is required

### Config package

```php
/*
 * Class name of models
 *
 * */
'model_role' => \App\Role::class,
'model_permission' => \App\Permission::class,
'model_user' => \App\User::class,
/*
 * Auto add Authorization to alias, if you want change or disable you can change in here.
 * This is equivalent to insert the following code to to aliases
 * 'Authorization' => \Buzz\Authorization\AuthorizationFacade::class,
 *
 * */
'auto_alias' => true,
'alias' => 'Authorization',
/*
 * Add blade shortcut: @permission, @role, @anyRole, ...
 *
 * */
'blade_shortcut' => true,
/*
 * If you do not want to use the role level, you can switch to false and remove field level in migration.
 * */
'user_level' => true,
/*
 * Exception class name is used in middleware
 * */
'role_exception' => \Buzz\Authorization\Exception\RoleDeniedException::class,
'permission_exception' => \Buzz\Authorization\Exception\PermissionDeniedException::class,
/*
 * level_exception will be required if option user_level is true and you use level middleware
 * */
'level_exception' => \Buzz\Authorization\Exception\LevelDeniedException::class
```

### Config model
##### Permission and Role models
Two models will appear after run command published successfully. You can edit, custom any thing you want, but not remove default trait available.

Example:

```php
// Permission.php
namespace App;

use Buzz\Authorization\Traits\PermissionAuthorizationTrait;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use PermissionAuthorizationTrait;
    public $table = 'permissions';
}

//Role.php
namespace App;

use Buzz\Authorization\Interfaces\RoleInterface;
use Buzz\Authorization\Traits\RoleAuthorizationTrait;
use Illuminate\Database\Eloquent\Model;

class Role extends Model implements RoleInterface
{
    use RoleAuthorizationTrait;
    public $table = 'roles';
}
```

##### User model
You need to remove trait ``Authorizable`` and contract ``AuthorizableContract`` (default of laravel). And after that, use two trait of the package, implements two interface ``UserAuthorizationInterface``, ``UserLevelInterface``
```php
Buzz\Authorization\Traits\UserAuthorizationTrait;
Buzz\Authorization\Traits\UserLevelTrait; //only add when you use role level
```

Example:
```php
namespace App;

use Buzz\Authorization\Interfaces\UserAuthorizationInterface;
use Buzz\Authorization\Interfaces\UserLevelInterface;
use Buzz\Authorization\Traits\UserAuthorizationTrait;
use Buzz\Authorization\Traits\UserLevelTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
    CanResetPasswordContract, UserAuthorizationInterface, UserLevelInterface
{
    use Authenticatable, CanResetPassword, UserAuthorizationTrait, UserLevelTrait;
}
```

## Instruction
### Creating data
##### Create Permission

```php
$permission = new \App\Permission();//depend "model_permission" config 
$permission->name = 'Create posts';
$permission->slug = 'post.create';// can use str_slug('Create posts', '.');
$permission->save();
```

##### Create Role

```php
$role = new \App\Role();//depend "model_role" config 
$role->name = 'Admin';
$role->slug = 'Admin';// can use str_slug('Create posts', '.');
$role->save();
```

### Attach/ detach data
#### Attach/ detach permissions

```php
//attach
$role->attachPermission($permission);//input is object
$role->attachPermission([$permission, $permission2, $permission3]);//input is array objects
$role->attachPermission(1);//assume 1 is id of $permission
$role->attachPermission([1,2,3]);//assume 1,2,3 is id of $permission, $permission2, $permission3

//detach
$role->detachPermission($permission);//input is object
$role->detachPermission([$permission, $permission2, $permission3]);//input is array objects
$role->detachPermission(1);//assume 1 is id of $permission
$role->detachPermission([1,2,3]);//assume 1,2,3 is id of $permission, $permission2, $permission3
$role->detachPermission([]);//detach all permissions
```

#### Attach/ detach roles

```php
$user = \App\User::find(1);//depend "model_user" config 
//attach
$role->attachRole($role);//input is object
$role->attachRole([$role, $role2, $role3]);//input is array objects
$role->attachRole(1);//assume 1 is id of $role
$role->attachRole([1,2,3]);//assume 1,2,3 is id of $role, $role2, $role3

//detach
$role->detachRole($role);//input is object
$role->detachRole([$role, $role2, $role3]);//input is array objects
$role->detachRole(1);//assume 1 is id of $role
$role->detachRole([1,2,3]);//assume 1,2,3 is id of $role, $role2, $role3
$role->detachRole([]);//detach all roles
```

### Checking role/ permission/ level
> Always return ``false`` if ``Auth::check() === false``

> All blade shortcuts available if config ``blade_shortcut`` is true

```php
//someAction: is, isAny, can, canAny
//check user with database
$user = \App\User::find(1);
$user->someAction
//check current user login
$user = \Auth::user();
$user->someAction
//or
Authorization::someAction
//or
app('authorization')->someAction
```
Check has role or has all roles
```php
$user->is('admin');//admin is slug of role
//OR
$user->is(['admin', 'mod']);//['admin', 'mod'] is array slugs of role
//Blade
@role('admin')
    //your code
@endRole
```
Check has one in any roles
```php
$user->isAny(['admin', 'mod']);
//Blade
@anyRole('admin')
    //your code
@endAnyRole
```
Check has permission or has all permissions
```php
$user->can('post.create');//admin is slug of permission
//OR
$user->can(['post.create', 'post.delete']);//['admin', 'mod'] is array slugs of permission
//Blade
@permission('post.create')
    //your code
@endPermission
```
Check has one in any permissions
```php
$user->canAny(['post.create', 'post.delete']);
//Blade
@anyPermission('post.create')
    //your code
@endAnyPermission
```
Check level (available if config ``user_level`` is true)
```php
@greaterLevel('3')// check smallest level of user > 3
    //your code
@endGreaterLevel
@endLessLevel('3')// check smallest level of user < 3
    //your code
@endGreaterLevel
@betweenLevel(3, 5)// check smallest level of user between 3 and 5
    //your code
@endBetweenLevel
@matchAnyLevel([3,5])// check smallest level of user has in array [3,5]
    //your code
@endMatchAnyLevel
```

### Check with Middleware
> Throw new exception if not match, you can change exception class in config with key permission_exception, role_exception, level_exception

Check permission
```php
//check user can delete post
Route::get('/permission', ['middleware' => ['permission:post.delete'], 'uses' => function () {
    return 'permission';
}]);
//check user can delete post and create post
Route::get('/permission', ['middleware' => ['permission:post.delete&post.create'], 'uses' => function () {
    return 'permission';
}]);
//check user can delete post or create post
Route::get('/permission', ['middleware' => ['permission:post.delete|post.create'], 'uses' => function () {
    return 'permission';
}]);
```

Check role
```php
//check user is admin
Route::get('/role', ['middleware' => ['role:admin'], 'uses' => function () {
    return 'role';
}]);
//check user is admin and mod
Route::get('/role', ['middleware' => ['role:admin&mod'], 'uses' => function () {
    return 'role';
}]);
//check user is admin or mod
Route::get('/role', ['middleware' => ['role:admin|mod'], 'uses' => function () {
    return 'role';
}]);
```

Check level: by default package get smallest level of user and compare, if you want use greatest level you can add prefix "max". Example:
- level:max1
- level:max1<=>3
- level:max<3
- ...

```php
//check level smallest of user equal 1
Route::get('/level', ['middleware' => ['level:1'], 'uses' => function () {
    return 'level';
}]);
//check 1 <= level smallest of user <= 3
Route::get('/level', ['middleware' => ['level:1<=>3'], 'uses' => function () {
    return 'level';
}]);
//check level smallest of user < 3
Route::get('/level', ['middleware' => ['level:<3'], 'uses' => function () {
    return 'level';
}]);
//check level smallest of user > 3
Route::get('/level', ['middleware' => ['level:>3'], 'uses' => function () {
    return 'level';
}]);
//check user has all level in list 1,2,3
Route::get('/level', ['middleware' => ['level:1&2&3'], 'uses' => function () {
    return 'level';
}]);
//check user has one level in list 1,2,3
Route::get('/level', ['middleware' => ['level:1|2|3'], 'uses' => function () {
    return 'level';
}]);
```


> Docs in the process of finalizing
