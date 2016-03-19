<?php
/**
*
*/

$app->get('/change-password', function ($request, $response) {
    //	Simply render the password change page.
    return $this->view->render($response, 'auth/password_change.twig');
})->setName('change_password')->add($authenticated);

$app->post('/change-password', function ($request, $response) {
    //	Grab the parameters from the request. This can be done directly from the request,
    //	but I personally like using the array. Feel free to change it.
    $posted = $request->getParams();

    //	The change password form requires the user to enter their old password, and a new
    //	one along with a confirmation of the new password. Create variables for the posted
    //	data.
    $old_password 		= $posted['old_password'];
    $password 			= $posted['password'];
    $password_confirm 	= $posted['password_confirm'];

    //	Grab our validation object.
    $v = $this->validation;

    //	Validate our values.
    //
    //	Old password is required and must match the current password of the user. This is
    //	okay to call because we require that the user be authenticated in order for them
    //	to change their password, so we know they're logged in as a user and our validation
    //	class can grab their user information.
    //
    //	The new password and password confirm must match and be of length 6 or more.
    $v->validate([
        'old_password'		=>	[$old_password, 'required|matchesCurrentPassword'],
        'password'			=>	[$password,	'required|min(6)'],
        'password_confirm' 	=> 	[$password_confirm, 'required|matches(password)']
    ]);

    //	Check if the validation passed.
    if ($v->passes())
    {
        //	Since it passed, we can grab the current user model from the auth dependency. Again,
        //	this is possible because the user is required to be authenticated to view the page.
        $user = $this->auth;

        //	Simply update the user's password to match what they want to change it to.
        $user->update([
            'password' => $this->hash->password($password)
        ]);

        //	Send the user an email to notify them that their password was changed.
        $this->mailer->send('templates/email/changed_password.twig', [], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('You changed your password');
        });

        //	Redirect the user to the homepage.
        return $response->withRedirect($this->router->pathFor('home'));
    }

    //	Our validation didn't pass, so re-render the passowrd change page and pass our errors.
    //	We don't need to pass the posted data as they're all passwords.
    return $this->view->render($response, 'auth/password_change.twig', [
        'errors' => $v->errors()
    ]);

})->setName('change_password.post')->add($authenticated);
