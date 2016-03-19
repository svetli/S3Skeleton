<?php
/**
*
*/

namespace App\Common\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserPermission extends Eloquent
{
    protected $table = 'users_permissions';

    protected $fillable = [
        'is_admin',
        'is_mod'
    ];

    public static $defaults = [
        'is_admin' => false,
        'is_mod'   => false
    ];

}
