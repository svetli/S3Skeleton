<?php
/**
*
*/

namespace App\Common\Validation;

use Violin\Violin;

class Validator extends Violin
{
	/**
	* @var \App\Common\User\User 	$user 	A user object that models the users table in our database.
	* @var \App\Common\Helpers\Hash $hash 	Hash helper.
	* @var \App\Common\User\User 	$auth 	Current logged in user. 
	*/
	protected $user;
	protected $hash;
	protected $auth;

	/**
	* Constructor
	*
	* @param \App\Common\User\User 		$user 	A user object that models the users table in our database.
	* @param \App\Common\Helpers\Hash 	$hash 	Hash helper.
	* @param \App\Common\User\User 		$auth 	Current logged in user. Default null.
	*/
	public function __construct($user, $hash, $auth = null)
	{
		$this->user = $user;
		$this->hash = $hash;
		$this->auth = $auth;
		$this->addFieldMessages([
				'email' => [
					'uniqueEmail' => 'That email is already in use.'
				],
				'username' => [
					'uniqueUsername' => 'That username is already in use.'
				]
			]);
		$this->addRuleMessages([
			'matchesCurrentPassword' => 'Current password is incorrect'
		]);
	} //End constructor

	/**
	* Checks whether or not the passed email parameter is already in use.
	*
	* @return bool 	True if the email is unique, false otherwise.
	*/
	public function validate_uniqueEmail($email, $input, $args)
	{
		return ! (bool) $this->user->where('email', $email)->count();
	} //End validate_uniqueEmail

	/**
	* Checks whether or not the passed username parameter is already in use
	*
	* @return bool 	True if the username is unique, false otherwise.
	*/
	public function validate_uniqueUsername($username, $input, $args)
	{
		return ! (bool) $this->user->where('username', $username)->count();
	} //End validate_uniqueUsername

	/**
	* Checks whether or not the password parameter matches the user's current
	* password. Makes use of the hash dependency for passwordCheck.
	*
	* @return bool 	True if the password matches the user's password, false otherwise.
	*/
	public function validate_matchesCurrentPassword($value, $input, $args)
	{
		if ($this->auth && $this->hash->passwordCheck($value, $this->auth->password)) {
			return true;
		}
		return false;
	} //End validate_matchesCurrentPassword
} //End class Validator