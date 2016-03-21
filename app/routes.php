<?php
/**
*
*/

if (!defined('IN_PROJECT'))
{
    exit;
}

require(GLOBAL_ROOT_PATH . '/app/routes/auth/auth_routes' . PHP_EXT);

$app->get('/', function ($request, $response)  use ($container) {

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
        'AllPost'   => $PostAll
    ]);
})->setName('home');

$app->get('/post/[{id:[0-9]+}/[{name:[A-Za-z-]+}]]', function ($request, $response, $args)  use ($container) {

    //	Feel free to change all of it. :)

    $id = $args['id'];
    $name = $args['name'];

    $posted = $container->post
        ->where('id','=', "$id")
        ->where('seo_url', '=',"$name")
        ->first();

    return $this->view->render($response, 'templates/post_detail.twig', [
        'post'     => $posted
    ]);

})->setName('post.detail');
