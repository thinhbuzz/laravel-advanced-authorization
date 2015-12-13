# Advanced authorization in Laravel 5.*

> This package inspired by [zizaco/entrust](https://github.com/Zizaco/entrust) and [bican/roles](https://github.com/romanbican/roles).

- [Installation](#installation)
    - [Composer](#composer.json)
    - [Provider](#provider)
    - [Alias](#alias)
    - [Middleware](#middleware)
- [Configuration](#configuration)
    - [Pubslish](#pubslish)
    - [Config package](#config_package)
    - [Config model](#config_model)
- [Instruction](#instruction)
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

```
\Buzz\Authorization\AuthorizationServiceProvider::class,
\Buzz\Authorization\UtilitieServiceProvider::class,
```
``UtilitieServiceProvider`` not required, this provider only help you publish models and seed of package by using the command. After publish you can remove it.

### Alias

By default, the package will automatically add ``Authorization`` to ``aliases``, you can edit in the config of the package.

### Middleware

Open ``app/Http/Kernel.php`` and insert into bellow code to routeMiddleware:

```
'role' => \Buzz\Authorization\Middleware\RoleMiddleware::class,
'permission' => \Buzz\Authorization\Middleware\PermissionMiddleware::class,
'level' => \Buzz\Authorization\Middleware\LevelMiddleware::class,
```

## Configuration
- So publish config, migration you can run command

```
php artisan vendor:publish --provider="\Buzz\Authorization\AuthorizationServiceProvider" --tag=config
php artisan vendor:publish --provider="\Buzz\Authorization\AuthorizationServiceProvider" --tag=migrations
```

And after that run command: ``php artisan migrate`` (maybe you want edit migration file before run this command)

- So publish seed, model you can run command: ``php artisan authorization:seeder`` and ``php artisan authorization:model``
To perform these commands, ``UtilitieServiceProvider`` added to ``providers`` (``config/app.php``) is required

### Config package

```
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
Ex:

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

use Buzz\Authorization\Traits\RoleAuthorizationTrait;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use RoleAuthorizationTrait;
    public $table = 'roles';
}
```
##### User model
You need to remove trait ``Authorizable`` and contract ``AuthorizableContract`` (default of laravel). And after that, use two trait of the package

```
Buzz\Authorization\Traits\UserAuthorizationTrait;
Buzz\Authorization\Traits\UserLevelTrait; //only add when you use role level
```

Ex:
```php
namespace App;
use Buzz\Authorization\Traits\UserAuthorizationTrait;
use Buzz\Authorization\Traits\UserLevelTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, UserAuthorizationTrait, UserLevelTrait;
}
```

## Instruction
### Creating data

##### Create Role
