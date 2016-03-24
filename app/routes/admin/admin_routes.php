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
        // Route for /users/{id:[0-9]+}/reset-password
        // Reset the password for user identified by $args['id']
    })->setName('admin');
});
