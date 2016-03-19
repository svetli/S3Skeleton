<?php
/**
*
*/

namespace App\Common\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserPermission extends Eloquent
{
    /**
    * @var string $table 				Name of the table that this class models.
    * @var array  $fillable 			Array of the columns this model can modify.
    */
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
