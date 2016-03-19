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
		'password_recover',
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
	public function getUsername()
	{
		return $this->username;
	} //End getUsername

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
	* Returns the user's user's gravatar url. Function currently isn't in use,
	* but no reason to delete it as of now.
	*
	* @return string 	Link to the user's gravatar image.
	*/
	public function getAvatarUrl($options = [])
	{
		//$size = isset($options['size']) ? $options['size'] : 45;
		$size = 45;
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
} //End class User