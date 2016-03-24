<?php
/**
*
*/

$app->get('/activate', function($request, $response) {
    //	Grab the parameters from the request. The URI has the user's email and
    //	activate identification string.
    $posted = $request->getParams();

    //	Assign the $email and $identifier variables their respective parameters.
    $email 		= $posted['email'];
    $identifier = $posted['identifier'];

    //	Hash the identifier so that it can be compared to our database.
    $hashed_identifier = $this->hash->hash($identifier);

    //	Grab the user that matches the email. User must be inactive to activate their account.
    $user = $this->user
        ->where('email', $email)
        ->where('is_active', false)
        ->first();

    //	Check that the email matches an inactive user & the hash matches that user's hash from the database.
    if ($user && $this->hash->hashCheck($user->active_hash, $hashed_identifier))
    {
        //	Since our conditions were met, we can safely activate the user's account.
        $user->activateAccount();

        // Flash Message
        $this->flash->addMessage('global', 'Your account has been activated and you can sign in');

        //	Regardless of whether the conditions were met, we want to redirect the user to the homepage.
        return $response->withRedirect($this->router->pathFor('home'));
    }
    else
    {
        // Flash Message
        $this->flash->addMessage('global', 'There was a problem activating your account');

        //	Regardless of whether the conditions were met, we want to redirect the user to the homepage.
        return $response->withRedirect($this->router->pathFor('home'));
    }
})->setName('activate')->add($guest);
