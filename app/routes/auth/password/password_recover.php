<?php
/**
*
*/

$app->get('/recover-password', function($request, $response) {
    return $this->view->render($response, 'auth/password_recover.twig');
})->setName('password_recover')->add($guest);

$app->post('/recover-password', function($request, $response) {
    //	Grab the parameters from the request. This can be done directly from the request,
    //	but I personally like using the array. Feel free to change it.
    $posted = $request->getParams();

    //	The password recovery form only requires the user to enter an email address. Create
    //	a variable for that.
    $email = $posted['email'];

    //	Grab our validation object.
    $v = $this->validation;

    //	Validate that the email address is actually an email.
    $v->validate([
        'email' => [$email, 'required|email']
    ]);

    //	Check if our validation passed.
    if ($v->passes())
    {
        //	Try to grab a user that matches the email address.
        $user = $this->user->where('email', $email)->first();

        //	Check if a user was found.
        if (!$user)
        {
            //	Since a user wasn't found, redirect the user back to the recovery page.
            //	Might want to flash a message here in the future.
            return $response->withRedirect($this->router->pathFor('password_recover'));
        }
        else
        {
            //	We're here, so we found a valid user. Now we should generate a random string
            //	to be used as a recovery attempt validator.
            $identifier = $this->randomlib->generateString(128);

            //	Store the hashed version of the string in the user's password_recover database
            //	column. We don't want to do anything else here, because all the user has proven
            //	so far is that they know what email address someone used to make an account.
            $user->update([
                'password_recover' => $this->hash->hash($identifier)
            ]);

            //	Send the account owner  an email with a link to the recovery form for their account.
            //	The link will be generated in the email template using the user and identifier parameters
            //	we pass in the array.
            $this->mailer->send('templates/email/password_recover.twig', [
                'user' => $user,
                'identifier' => $identifier
            ], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Password recovery request');
            });

            // Flash Message
            $this->flash->addMessage('global', 'We have emailed you instructions to reset your password');

            //	Redirect to the homepage.
            return $response->withRedirect($this->router->pathFor('home'));
        }
    }

    return $this->view->render($response, 'auth/password_recover.twig', [
        'errors' => $v->errors(),
        'posted' => $posted
    ]);
})->setName('password_recover.post')->add($guest);
