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
        'id_admin',
        'id_mod'
    ];

    public static $defaults = [
        'id_admin' => false,
        'id_mod'   => false
    ];

}
