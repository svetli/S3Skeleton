<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
    exit;
}

require(GLOBAL_ROOT_PATH . '/app/routes/auth/auth_routes' . PHP_EXT);
require(GLOBAL_ROOT_PATH . '/app/routes/admin/admin_routes' . PHP_EXT);

$app->get('/', function ($request, $response)  use ($container) {

    // Get flash messages from previous request
    $messages = $this->flash->getMessages();

    $PostAll = $container->post
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->get();

    $posted = $container->post
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();

    return $this->view->render($response, 'home.twig', [
        'posts'     => $posted,
        'AllPost'   => $PostAll,
        'messages' => $messages
    ]);

})->setName('home');

$app->get('/post/[{id:[0-9]+}/[{name:[A-Za-z-]+}]]', function ($request, $response, $args)  use ($container) {

    //	Feel free to change it. :)

    $id = $args['id'];
    $name = $args['name'];

    $posted = $container->post
        ->where('id','=', "$id")
        ->where('seo_url', '=',"$name")
        ->first();
    if ($posted)
    {
        return $this->view->render($response, 'templates/post_detail.twig', [
            'post'     => $posted
        ]);
    }
    else
    {
        // TODO
        // Handle 404 for now, redirect to home
        return $response->withRedirect($this->router->pathFor('home'));
    }

})->setName('post.detail');
