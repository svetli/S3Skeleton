<?php
/**
*
*/

use Illuminate\Database\Capsule\Manager as Capsule;

//	Create a capsule to use Eloquent.
$container['db'] = function ($container) {
    $capsule = new Capsule;
    $capsule->addConnection([
        'driver' 	=>	$container->config->get('db.driver'),
        'host'		=>	$container->config->get('db.host'),
        'database' 	=>	$container->config->get('db.name'),
        'username' 	=> 	$container->config->get('db.username'),
        'password' 	=>	$container->config->get('db.password'),
        'charset'	=>	$container->config->get('db.charset'),
        'collation'	=>	$container->config->get('db.collation'),
        'prefix'	=>	$container->config->get('db.prefix')
    ]);
    $capsule->bootEloquent();
    return $capsule;
};

