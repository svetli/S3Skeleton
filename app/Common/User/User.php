<?php
/**
*
*/

namespace App\Common\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    /**
    * @var string $table 				Name of the table that this class models.
    * @var array  $fillable 			Array of the columns this model can modify.
    */
    protected $table = 'users';
    protected $fillable = [
        'email',
        'username',
        'password',
        'recover_hash',
        'is_active',
        'active_hash',
        'remember_identifier',
        'remember_token',
    ];

    /**
    * Returns the user's username/displayname
    *
    * @return string 	The username.
    */
    public function getId()
    {
        return $this->id;
    } //End getUsername

    /**
    * Returns the user's username/displayname
    *
    * @return string 	The username.
    */
    public function getUsername()
    {
        return $this->username;
    } //End getUsername

    /**
    * Returns the user's full name
    *
    * @return string 	The full name.
    */
    public function getFullName()
    {
        if (!$this->first_name || !$this->last_name)
        {
            return null;
        }
        return "{$this->first_name} {$this->last_name}";
    } //End getFullName

    /**
    * Returns the user's first name
    *
    * @return string 	The first name.
    */
    public function getFirstName()
    {
        if (!$this->first_name)
        {
            return null;
        }
        return "{$this->first_name}";
    } // End getFirstName

    /**
    * Returns the user's last name
    *
    * @return string 	The last name.
    */
    public function getLastName()
    {
        if (!$this->last_name)
        {
            return null;
        }
        return "{$this->last_name}";
    } // End getLastName

    /**
    * Returns the user's full name or username
    *
    * @return string 	full name or username.
    */
    public function getFullNameOrUsername()
    {
        return $this->getFullName() ?: $this->username;
    } // End getFullNameOrUsername

    /**
    * Activates the user's account.
    *
    * @return null
    */
    public function activateAccount()
    {
        $this->update([
            'is_active' => true,
            'active_hash' => null
        ]);
    } //End activateAccount

    /**
    * Returns the user's gravatar url.
    *
    * @return string Link to the user's gravatar image.
    */
    public function getAvatarUrl($options = [])
    {
        $size = isset($options['size']) ? $options['size'] : 45;
        return 'http://www.gravatar.com/avatar/' . md5($this->email) . '?s=' . $size . '&d=mm';
    } //End getAvatarUrl

    /**
    * Updates the user's remember me token & identifier. No checking is done.
    * Parameters should be validated before calling this function.
    *
    * @param string $identifier 	Hashed identifier string.
    * @param string $token 			Hashed token string.
    * @return null
    */
    public function updateRememberCredentials($identifier, $token)
    {
        $this->update([
            'remember_identifier' => $identifier,
            'remember_token' => $token
        ]);
    } //End updateRememberCredentials

    /**
    * Null's out the remember credentials of the user.
    *
    * @return null
    */
    public function removeRememberCredentials()
    {
        $this->updateRememberCredentials(null, null);
    } //End removeRememberCredentials

    /**
    * Returns the user's permission
    *
    * @return
    */
    public function permissions()
    {
        return $this->hasOne('\App\Common\User\UserPermission', 'user_id');
    } // End permissions

    /**
    * Checks whether the $permission column of the user_permissions table is true or false.
    *
    * @param string $permission 	The name of the permission to check for.
    * @return bool 					True if the user has the permission, false otherwise.
    */
    public function hasPermission($permission)
    {
        return (bool) $this->permissions->{$permission};
    } // End hasPermission

    /**
    * Returns the user is admin
    *
    * @return bool
    */
    public function isAdmin()
    {
        return $this->hasPermission('is_admin');
    } // End isAdmin

    /**
    * Returns the user is mod
    *
    * @return bool
    */
    public function isMod()
    {
        return $this->hasPermission('is_mod');
    } // End isMod

    /**
    * Returns the user is admin or mod
    *
    *
    */

    public function isAdminOrMod()
    {
        return $this->isAdmin() || $this->isMod();
    } // End isAdminOrMod

    /**
    * Returns the user's post
    *
    * @return
    */
    public function posts()
    {
        return $this->hasMany('\App\Common\Post\Post');
    } // End posts

} //End class User
