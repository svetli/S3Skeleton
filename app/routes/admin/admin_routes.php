<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
    exit;
}


$app->group('/dash', function () {

    $this->get('/', function ($request, $response, $args) {
        echo 'ola ke ase';
    })->setName('admin');

});
