<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
	exit;
}

require(GLOBAL_ROOT_PATH . '/app/routes/auth/register' 			. PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/auth/login' 			. PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/auth/activate' 			. PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/auth/logout' 			. PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/auth/password_change' 	. PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/auth/password_recover' 	. PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/auth/password_reset' 	. PHP_EXT);
