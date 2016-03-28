<?php
/**
*
*/

$app->get('/password-reset', function ($request, $response) {
    //	Unlike other authentication routes, this get route will need to have actual logic be executed.
    //	This is because the url contains important information that must be validated for the reset to
    //	be allowed to proceed. The request will have an email and identifier parameter, so we create
    //	variables for them and grab their values.
    $email 		= $request->getParam('email');
    $identifier = $request->getParam('identifier');

    //	The identifier in the url is an unhashed version of what's (possibly) in our database. Create a
    //	hashed version of it for comparison.
    $hashed_identifier = $this->hash->hash($identifier);

    //	Try to find a user with the email address from the url.
    $user = $this->user->where('email', $email)->first();

    //	Perform three checks here. If any fail, redirect the user to the homepage.
    //	1) 	Does the user exist?
    //	2) 	Does the user have a non-null recover_hash column? (This means the user has an open password
    //		recovery request.)
    //	3) 	Does the hashed version of the identifier taken from the url match the recover identifier stored
    //		in the database?
    if (!$user || !$user->recover_hash || !$this->hash->hashCheck($user->recover_hash, $hashed_identifier))
    {
        // Flash Message
        $this->flash->addMessage('global', 'We have problem reseting your password... ');
        return $response->withRedirect($this->router->pathFor('home'));
    }

    //	Since none of the checks failed, we can render the form for the user to reset their password. We want
    //	to pass the email and identifier parameters to the view, though, so that the url can be maintained as
    //	the user submits the form and/or makes data-entry errors.
    return $this->view->render($response, 'auth/password_reset.twig', [
        'email' 		=> $user->email,
        'identifier' 	=> $identifier
    ]);
})->setName('password.reset')->add($guest);

$app->post('/password-reset', function ($request, $response) {
    //	Grab the parameters from the request. This can be done directly from the request,
    //	but I personally like using the array. Feel free to change it.
    $posted = $request->getParams();

    //	The password reset form has two options: password and password confirm. We need to
    //	create variables for those entries and also the email and identifier parameters that
    //	are in the url.
    $email 				= $posted['email'];
    $identifier 		= $posted['identifier'];
    $password 			= $posted['password'];
    $password_confirm 	= $posted['password_confirm'];

    //	The identifier in the url is an unhashed version of what's (possibly) in our database. Create a
    //	hashed version of it for comparison.
    $hashed_identifier = $this->hash->hash($identifier);

    //	Try to find a user with the email address from the url.
    $user = $this->user->where('email', $email)->first();

    //	Perform three checks here. If any fail, redirect the user to the homepage.
    //	1) 	Does the user exist?
    //	2) 	Does the user have a non-null recover_hash column? (This means the user has an open password
    //		recovery request.)
    //	3) 	Does the hashed version of the identifier taken from the url match the recover identifier stored
    //		in the database?
    if (!$user || !$user->recover_hash || !$this->hash->hashCheck($user->recover_hash, $hashed_identifier))
    {
        // Flash Message
        $this->flash->addMessage('global', 'We have problem reseting your password... ');
        return $response->withRedirect($this->router->pathFor('home'));
    }

    //	Since none of the checks failed, the user is attempting a valid password-reset. Let's grab our
    //	validation object.
    $v = $this->validation;

    //	Validate the user's new password. It should match the password confirm and be longer than six characters.
    $v->validate([
        'password' 			=> [$password, 'required|min(6)'],
        'password_confirm' 	=> [$password_confirm, 'required|matches(password']
    ]);

    //	Check if the validation passes.
    if ($v->passes())
    {
        //	The validation passed, so we can simply update the user's password in the database. We also need
        //	to null out the password_recover column so that no more reset attempts can be made without another
        //	request being submitted.
        $user->update([
            'password' 			=> $this->hash->password($password),
            'recover_hash' 	=> null
        ]);

        // Flash Message
        $this->flash->addMessage('global', 'Your password has been reset and you can now sign in');

        //	Redirect the user to the homepage.
        return $response->withRedirect($this->router->pathFor('home'));
    }

    //	Our validation failed, so we want to re-render the reset form, passing the validation errors and the
    //	email/identifier parameters to maintain the url integrity.
    return $this->view->render($response, 'auth/password_reset.twig', [
        'email'			=> $user->email,
        'identifier' 	=> $identifier,
        'errors'		=> $v->errors()
    ]);
})->setName('password_reset.post')->add($guest);
