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


$app->get('/dash/u/{username}', function ($request, $response, $args)  use ($container) {

    // Get flash messages from previous request
    $messages = $this->flash->getMessages();

    $username = $args['username'];

    $user = $container->user
        ->where('username', $username)
        ->first();

    if (!$user) {
        // TODO
        // Handle 404 for now, redirect to home
        return $response->withRedirect($this->router->pathFor('home'));
    }

    return $this->view->render($response, 'admin/templates/user.html', [
        'messages' => $messages
    ]);

})->setName('user-profile')->add($authenticated);

$app->post('/dash/u/{username:[A-Za-z-]+}', function ($request, $response, $args)  use ($container) {

echo 'posted';

})->setName('user-profile-post')->add($authenticated);
