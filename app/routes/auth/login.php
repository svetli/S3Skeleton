<?php
/**
*
*/

use Carbon\Carbon;

$app->get('/login', function ($request, $response) {
    //	Simply render the login page.
    return $this->view->render($response, 'auth/login.twig');
})->setName('login')->add($guest);

$app->post('/login', function ($request, $response) {
    //	Grab the parameters from the request. This can be done directly from the request,
    //	but I personally like using the array. Feel free to change it.
    $posted = $request->getParams();

    //	The login form only reqwuires the user to enter their username or email address and
    //	their password, so create variables for them.
    $identifier = $posted['identifier'];
    $password 	= $posted['password'];

    //	Check if remember me was ticked. This is here to prevent an exception from being thrown
    //	later from trying to access a null index.
    if (!isset($posted['remember']))
    {
        $posted['remember'] = false;
    }

    //	Now that we know remember is set one way or another we can create a variable for it.
    $remember = $posted['remember'];

    //	Grab our validation object.
    $v = $this->validation;

    //	The only requirements we have for the form was that the identifier & password fields were
    //	filled out, so check for that.
    $v->validate([
        'identifier' 	=> [$identifier, 'required'],
        'password' 		=> [$password, 'required']
    ]);

    //	Now check if our validation passed.
    if ($v->passes())
    {
        //	Since it passed, try to grab the user whose email or username matches the identifier
        //	and is active.
        $user = $this->user
            ->where('is_active', true)
            ->where(function($query) use ($identifier)
                    {
                        return $query
                            ->where('email', $identifier)
                            ->orWhere('username', $identifier);
                    })
            ->first();

        //	Set our auth to equal the user object we just pulled from the db. If we didn't find
        //	a user, auth will be assigned null.
        $this->auth = $user;

        //	Check if we found a user and then whether or not the password submitted matches the
        //	one stored in our database.
        if ($user && $this->hash->passwordCheck($password, $user->password))
        {
            //	The user entered correct credentials, so we're going to log them in. However, we
            //	should first check if they toggled on persistent login.
            if ($remember == 'on')
            {
                //	Since persistent login was toggled on, we want to create two different randomly
                //	generated strings to keep the autologin session secure.
                $remember_identifier = $this->randomlib->generateString(128);
                $remember_token = $this->randomlib->generateString(128);

                //	We want to assign the strings to our remember credential columns in the database.
                //	The identifier string will be stored as plain text, while the token will be hashed.
                $user->updateRemembercredentials($remember_identifier, $this->hash->hash($remember_token));

                //	Now, set a cookie whose name matches our config's presistent login setting. The
                //	cookie's data will be the two strings separated by three underscoures. Lastly,
                //	set a time for the cookie to expire. Change the '+1 month' to modify the time.
                //
                //	It's probably worth finding a third-party solution to storing cookies in the future.
                //	Slim 3 removed the cookie features they had in version 2, but they've said that they
                //	want to add some form of psr7 cookies to Slim 3 in the future. This is essentially a
                //	placeholder until that functionality is released.
                setcookie($this->config->get('auth.remember'), "{$remember_identifier}___{$remember_token}", Carbon::parse('+1 month')->timestamp);
            }

            //	Set the session key via our config with the value being the user's id number.
            $_SESSION[$this->config->get('auth.session')] = $user->id;

            //	Redirect the user to the homepage now that they're logged in.
            return $response->withRedirect($this->router->pathFor('home'));
        }
    }

    //	Since our validation didn't pass, we want to reload the page passing
    //	the errors and posted values. We pass the errors for obvious reasons,
    //	and we pass the posted data to the view so we can autofill sections of
    //	the form that the user didn't mess up.
    return $this->view->render($response,'auth/login.twig', [
        'errors' 	=> $v->errors(),
        'posted' 	=> $posted
    ]);
})->setName('login.post')->add($guest);
