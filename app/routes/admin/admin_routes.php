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

      // Redirect to 404
      $errorHandler = $this->notFoundHandler;
      return $errorHandler($request, $response);
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

$app->get('/dash/new-post', function ($request, $response, $args) {


    $cat = $this->category->
        orderBy('name', 'desc')
        ->get();

    return $this->view->render($response, 'admin/templates/blog_post.html', [
        'categories' => $cat
    ]);

})->setName('new-blog-post')->add($both);

$app->post('/dash/new-post', function ($request, $response, $args) {

    //	Grab the parameters from the request. This can be done directly from the request,
    //	but I personally like using the array. Feel free to change it.
    $posted = $request->getParams();

    //	The blog form only requires the user to enter some values
    //  so create variables for them.
    $title      = $posted['title'];
    $excerpt 	  = $posted['excerpt'];
    $content 	  = $posted['content'];
    $status 	  = isset($posted['status']);
    $categories = $posted['categories'];
    $user_id 	  = $posted['user_id'];

    if (empty($status)) {
        $status = 0;
    }

    //	Grab our validation object.
    $v = $this->validation;

    $v->validate([
        'title'    => [$title, 'required'],
        'excerpt'  => [$excerpt, 'required'],
        'content'  => [$content, 'required'],
        'status'   => [$status, 'required']
    ]);
    //	Now check if our validation passed.
    if ($v->passes())
    {
        $storage = new \Upload\Storage\FileSystem(GLOBAL_ROOT_PATH . '/public/uploads');
        $file = new \Upload\File('my_image', $storage);
        $new_filename = uniqid();
        $file->setName($new_filename);
        $file->addValidations(array(
            new \Upload\Validation\Mimetype(array('image/png', 'image/gif','image/jpeg','image/jpg')),
            new \Upload\Validation\Size('5M')
        ));
        $data = array(
            'name'       => $file->getNameWithExtension(),
            'extension'  => $file->getExtension(),
            'mime'       => $file->getMimetype(),
            'size'       => $file->getSize(),
            'md5'        => $file->getMd5(),
            'dimensions' => $file->getDimensions()
        );
        try {
            $file->upload();
        } catch (\Exception $e) {
            $errors = $file->getErrors();
        }
        $this->post->create([
            'title'       => $title,
            'excerpt'     => $excerpt,
            'body'        => $content,
            'user_id'     => $user_id,
            'status'      => $status,
            'image'       => $file->getNameWithExtension(),
            'seo_url'     => post_slug($title),
            'category_id' => $categories
        ]);

        // Flash Message
        $this->flash->addMessage('global', '¡GREAT!');
        //	Redirect the user to the admin
        return $response->withRedirect($this->router->pathFor('admin'));

    } // End validation passed

    //	Since our validation didn't pass, we want to reload the page passing
    //	the errors and posted values. We pass the errors for obvious reasons,
    //	and we pass the posted data to the view so we can autofill sections of
    //	the form that the user didn't mess up.
    return $this->view->render($response, 'admin/templates/blog_post.html', [
      'errors' 	=> $v->errors(),
      'posted' 	=> $posted
    ]);

})->setName('add-new-blog-post')->add($both);
