<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
    exit;
}



$app->get('/dash', function ($request, $response)  use ($container) {

    // Get flash messages from previous request
    $messages = $this->flash->getMessages();

    return $this->view->render($response, 'admin/templates/dashboard.html', [
        'messages' => $messages
    ]);

})->setName('admin');
