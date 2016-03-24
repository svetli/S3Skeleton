<?php
/**
*
*/

$app->get('/logout', function ($request, $response, $args) {
    //	Logging a user out is very simple. We just unset the session.
    unset($_SESSION[$this->config->get('auth.session')]);

    //	Now, they're logged out, but we also need to manage their auto-
    //	login functionality. If a user manually logs out, we can assume
    //	they don't want to be automatically logged back in on their next
    //	visit. So, check if the user has a persistent login cookie set.
    if (isset($_COOKIE[$this->config->get('auth.remember')]))
    {
        //	Since they had persistent login enabled, call our function
        //	that nulls out the remember credentials of the database.
        $this->auth->removeRememberCredentials();

        //	Lastly, remove the cookie we set earlier.
        setcookie($this->config->get('auth.remember'), null, 1);
    }
    // Flash Message
    $this->flash->addMessage('global', 'You have been logged out');

    //	Redirect the user to the homepage.
    return $response->withRedirect($this->router->pathFor('home'));
})->setName('logout')->add($authenticated);
