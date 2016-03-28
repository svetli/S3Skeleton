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

})->setName('admin')->add($authenticated);


$app->get('/dash/u/{username}', function ($request, $response, $args) {

    // Get flash messages from previous request
    $messages = $this->flash->getMessages();

    $username = $args['username'];

    $user = $this->user
        ->where('username', $username)
        ->first();

    if (!$user) {
        // TODO
        // Handle 404 for now, redirect to home
        return $response->withRedirect($this->router->pathFor('home'));
    }

    return $this->view->render($response, 'admin/templates/user.html', [
        'user'      => $user,
        'messages'  => $messages
    ]);

})->setName('user-profile')->add($authenticated);

$app->post('/dash/u/{username}', function ($request, $response, $args)  use ($container) {

    echo 'posted';

})->setName('user-profile-post')->add($authenticated);

$app->get('/dash/users', function ($request, $response)  use ($container) {

    // Get flash messages from previous request
    $messages = $this->flash->getMessages();

    $users = $this->user->get();

    $user = $this->user
        ->orderBy('username', 'desc')
        ->take(5)
        ->get();

    return $this->view->render($response, 'admin/templates/userlist.html', [
        'messages' => $messages,
        'user'      => $user,
        'users'     => $users
    ]);

})->setName('user-list');

$app->get('/dash/users/{number}', function ($request, $response, $args) {

    // Get flash messages from previous request
    $messages = $this->flash->getMessages();

    $number = $args['number'];

    $users = $this->user->get();
    $pages = intval(ceil(count($users) / 5));
    $user = $this->user
        ->orderBy('username', 'desc')
        ->skip(5 * ($number - 1))
        ->take(5)
        ->get();

    return $this->view->render($response, 'admin/templates/user_list.html', [
        'messages'  => $messages,
        'user'      => $user,
        'users'     => $users,
        'pages'     => $pages,
        'number'    => $number
    ]);

})->setName('user-list-page');
