<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
	exit;
}

require(GLOBAL_ROOT_PATH . '/app/routes/auth/auth_routes' . PHP_EXT);

$app->get('/', function ($request, $response, $args) {
	return $this->view->render($response, 'home.twig', [
		'args' => $args,
	]);
})->setName('home');