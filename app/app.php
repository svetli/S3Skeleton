<?php
/**
*
*/

//	Use the psr request & response namespaces.
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \App\Common\Helpers\Hash;
use \Slim\Container;
use \Noodlehaus\Config;
use \Slim\Views\Twig;
use \Slim\Views\TwigExtension;
use \App\Common\User\User;
use \App\Common\Post\Post;
use \App\Common\Post\Category;
use \App\Common\Validation\Validator;
use \RandomLib\Factory;
use \Slim\App;
use \App\Common\Mail\Mailer;

//	Set the global to let all other files know they can run properly.
define('IN_PROJECT', true);
//	For development purposes, display the errors. Set to off for production environments.
ini_set('display_errors', 'On');
//	Create the global root path & php extension global variables.
define('GLOBAL_ROOT_PATH', dirname(__DIR__));
define('PHP_EXT', '.php');
define('MODE', file_get_contents(GLOBAL_ROOT_PATH . '/mode' . PHP_EXT));
//	Start our session. May create a custom session in the future.
session_cache_limiter(false);
session_start();
//	Autoload the dependencies.
require(GLOBAL_ROOT_PATH . '/vendor/autoload' . PHP_EXT);
//	This setting manages the Slim debugging. Set to false for production environments.
$configuration = [
    'settings' => [
        'displayErrorDetails' => true
    ]
];
//	We need to set up the container before creating our Slim instance.
$container = new Container($configuration);

//	Attach our config to the container using the Noodlehaus Config class.
$container['config'] = function ($container) {
    return new Config([GLOBAL_ROOT_PATH . '/config' . '/' . MODE . PHP_EXT]);
};
//	Setup the Twig views.
$container['view'] = function ($container) {
    $view = new Twig(GLOBAL_ROOT_PATH . '/app/views');
    $view->addExtension(new TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['view']->render($response, 'error/404.twig')->withStatus(404);
    };
};

//	Require Eloquent.

require(GLOBAL_ROOT_PATH . '/app/database' . PHP_EXT);

//	Inject our user model into the container.
$container['user'] = function ($container) {
    return new User;
};

//	Inject our post model into the container.
$container['post'] = function ($container) {
    return new Post;
};

//	Inject our post model into the container.
$container['category'] = function ($container) {
    return new Category;
};

//	Create our hashing helper.
$container['hash'] = function ($container) {
    return new Hash($container->config->get('app.hash'));
};

//	Create an auth object in the container and initially set it to false.
//	We'll set it to match the user's model when they log in or if they have
//	persistent login enabled.
$container['auth'] = function ($container) {
    return false;
};

//	Create our validation helper.
$container['validation'] = function ($container) {
    return new Validator($container->user, $container->hash, $container->auth);
};

//	Inject our mail feature into the container. Using PHPMailer for now.
$container['mailer'] = function ($container) {
    $mailer = new PHPMailer;

    $mailer->Host = $container->config->get('mail.host');
    $mailer->SMTPAuth = $container->config->get('mail.smtp_auth');
    $mailer->SMTPSecure = $container->config->get('mail.smtp_secure');
    $mailer->Port = $container->config->get('mail.port');
    $mailer->Username = $container->config->get('mail.username');
    $mailer->Password = $container->config->get('mail.password');
    $mailer->SMTPDebug = 1;
    $mailer->isHTML($container->config->get('mail.html'));

    return new Mailer($container->view, $mailer);
};

//	Add our helper for generating random strings.
$container['randomlib'] = function ($container) {
    $factory = new Factory;
    return $factory->getMediumStrengthGenerator();
};

//	Include our route-specific middleware.
require(GLOBAL_ROOT_PATH . '/app/route_middleware' . PHP_EXT);

//	And now instantiate our Slim instance passing in the container.
$app = new App($container);

//	We now want to add our application middleware to the Slim instance. They are executed
//	in the opposite order they're added from here. So the middleware at the bottom of the
//	file happens first.
//	Remove any trailing slashes from the uri.

$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        return $response->withRedirect((string)$uri, 301);
    }

    return $next($request, $response);
});

//	Check if the user is authenticated.
$app->add(function ($request, $response, $next) {
    //	Check if the session is set.
    if (isset($_SESSION[$this->config->get('auth.session')]))
    {
        //	Since it's set, just grab the user model and assign it to $this->auth.
        $this->auth = $this->user->where('id', $_SESSION[$this->config->get('auth.session')])->first();
    }
    else
    {
        //	It's not set, so $this->auth is false. This was already defined in the container declaration,
        //	but we do this here to prevent any possible errors & because it doesn't affect performance by
        //	any notable amount.
        $this->auth = false;
    }

    //	Pass data to all views. Auth is self-explanatory, baseUrl is just the domain so that we can generate
    //	correct links in our views.
    $this->view['auth'] = $this->auth;
    $this->view['baseUrl'] = $this->config->get('app.url');

    return $next($request, $response);
});

//	Manage persistent login.
$app->add(function ($request, $response, $next) {
    //	If the persistent login cookie isn't set, we don't need to do anything here.
    if (isset($_COOKIE[$this->config->get('auth.remember')]))
    {
        //	Since it is set, grab the value of the cookie and explode it into an array
        //	of two strings.
        $value = $_COOKIE[$this->config->get('auth.remember')];
        $credentials = explode('___', $value);

        //	Make sure that the cookie was formatted correctly. If it isn't, remove the
        //	cookie and redirect to the homepage.
        if (empty(trim($value)) || count($credentials) !== 2)
        {
            setcookie($this->config->get('auth.remember'), null, 1);
            return $response->withRedirect($this->router->pathFor('home'));
        }

        //	We've now established that the strings are at least of the correct format.
        //	That means we can grab the identifier and token strings from the array. We
        //	hash the token so that it matches the database.
        $identifier = $credentials[0];
        $token = $this->hash->hash($credentials[1]);

        //	Try to grab the user model that matches the remember identifier.
        $user = $this->user->where('remember_identifier', $identifier)->first();

        //	If we find a user, we can try to log them in.
        if ($user)
        {
            //	We already know that identifier matches the user, now we need to
            //	compare the token.
            if ($this->hash->hashCheck($token, $user->remember_token))
            {
                //	Set the session & pass the user's model into the auth object.
                $_SESSION[$this->config->get('auth.session')] = $user->id;
                $this->auth = $this->user->where('id', $user->id)->first();
            }
            else
            {
                //	Remove the user's remember credentials and delete the cookie.
                $user->removeRememberCredentials();
                setcookie($this->config->get('auth.remember'), null, 1);
            }
        }
        else
        {
            //	Since we found the cookie, but not the user, we can delete the cookie
            //	as it's obviously invalid.
            setcookie($this->config->get('auth.remember'), null, 1);
        }
    }
    return $next($request, $response);
});

//	CSRF protection.

$app->add(function ($request, $response, $next) {
    //	Grab the key from our config.
    $key = $this->config->get('csrf.key');

    //	Check if the session has a value for our key.
    if (!isset($_SESSION[$key]))
    {
        //	Since it doesn't, we assign a new randomly generated string.
        $_SESSION[$key] = $this->hash->hash($this->randomlib->generateString(128));
    }

    //	Grab the value from our session now that we're sure it's set.
    $token = $_SESSION[$key];

    //	Only execute this for POST, PUT, and DELETE requests.
    if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE']))
    {
        //	Get the value corresponding to $key from the request and then assign it to
        //	our $submitted_token variable. If the $key doesn't exist from the request
        //	it means we have a CSRF attempt, so we assign a simple empty string.
        $posted = $request->getParams();
        $submitted_token = $posted[$key] ?: '';

        //	Now we check the token from the request with the one from the session.
        if (!$this->hash->hashCheck($token, $submitted_token))
        {
            //	Since they don't match, throw a CSRF token exception.
            throw new \Exception('CSRF token mismatch');
        }
    }
    //	Share the csrf key & token values with all views so they can be
    //	put in forms.
    $this->view['csrf_key'] = $key;
    $this->view['csrf_token'] = $token;

    return $next($request, $response);
});


//	Start our database connection. When we injected our capsule into the container
//	it wasn't actually initialized. The callback is only ran after $this->db is called.
//	Since we don't actually use the $db injection for anything because we're working
//	with Eloquent's models, we need to set up middleware to get the database up and
//	running at the start of the application. This middleware should always be the first
// 	to run.
$app->add(function ($request, $response, $next) {
    $db = $this->db;
    return $next($request, $response);
});

// Appending sata to a view
$app->add(function($request, $response, $next) {
    $this->get('view')->getEnvironment()->addGlobal(
        'base_url', $this->config->get('app.url')
    );
    return $next($request, $response);
});
//	Here we include our general functions or constants that we need, along with our routes.
require(GLOBAL_ROOT_PATH . '/app/routes' . PHP_EXT);
