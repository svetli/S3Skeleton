<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
    exit;
}


$app->get('/error', function ($request, $response)  use ($container) {
    return $this->view->render($response, 'error/404.twig');
})->setName('error');
