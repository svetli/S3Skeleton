<?php
/**
*
*/

namespace App\Common\Helpers;

class Hash
{
	/**
	* @var array $config 	Array of config values relating to hashing for the application.
	*/
	protected $config;

	/**
	* Constructor.
	*
	* @param array $config 	Array of config values relating to hashing for the application.
	*/
	public function __construct($config)
	{
		$this->config = $config;
	} //End constructor

	/**
	* Hash a plain text password.
	*
	* @param string $password 	The plain text password to hash.
	* @return string 			The hashed $password parameter.
	*/
	public function password($password)
	{
		return password_hash($password, $this->config['algo'], ['cost' => $this->config['cost']]);
	} //End password

	/**
	* Check a password against it's hash.
	*
	* @param string $password 	Plain text password.
	* @param string $hash 		Hashed password.
	* @return bool 			 	True if they match, false otherwise.
	*/
	public function passwordCheck($password, $hash)
	{
		return password_verify($password, $hash);
	} //End password

	/**
	* Use sha256 to hash an input string.
	*
	* @param string $input 	Plain text string to be hashed.
	* @return string 		The hashed version of the $input string.
	*/
	public function hash($input)
	{
		return hash('sha256', $input);
	} //End hash

	/**
	* Check a hash against another. This is a function from the PHP
	* library but it doesn't want to work, so I copied the code directly.
	*
	* @param string $known 	The hash we know is correct.
	* @param string $user 	The hash we want to compare with it.
	* @return bool 			True if the hashed strings match, false otherwise.
	*/
	public function hashCheck($known, $user)
	{
       	if(strlen($known) != strlen($user))
       	{
           		return false;
       	}
       	else
       	{
           		$res = $known ^ $user;
           		$ret = 0;
           		for($i = strlen($res) - 1; $i >= 0; $i--)
           		{
               		$ret |= ord($res[$i]);
           		}
           		return !$ret;
       	}
	} //End hashCheck
} //End class Hash