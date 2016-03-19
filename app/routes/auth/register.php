<?php
/**
*
*/

$app->get('/register', function ($request, $response, $args) {
	return $this->view->render($response, 'auth/register.twig', [
		'args' 		=> $args,
		'posted' 	=> null
		]);
})->setName('register')->add($guest);

$app->post('/register', function ($request, $response, $args) {
	//	Grab the parameters from the request. This can be done directly from the request,
	//	but I personally like using the array. Feel free to change it.
	$posted = $request->getParams();

	//	The register form requires the user to enter an email, username, and password along
	//	with a confirmation of the password. Create variables for all of these.
	$email 				= $posted['email'];
	$username 			= $posted['username'];
	$password 			= $posted['password'];
	$password_confirm 	= $posted['password_confirm'];

	//	Grab out validation object.
	$v = $this->validation;

	//	Validate the user's input. All fields are required. The email must be a valid email,
	//	must be 100 or less characters, and must be unique within our database. The username
	//	must be unique, a maximum of 24 characters, and a combination of alphanumeric symbols
	//	and dashes. The password and password confirm must match and be 6 or more characters.
	$v->validate([
		'email' 			=> [$email, 'required|email|max(100)|uniqueEmail'],
		'username' 			=> [$username, 'required|alnumDash|max(24)|uniqueUsername'],
		'password' 			=> [$password, 'required|min(6)'],
		'password_confirm' 	=> [$password_confirm, 'required|matches(password)']
	]);

	//	Check if the validation passed.
	if ($v->passes())
	{
		//	Since it passed, we need to create a random string to use for account activation.
		//	We'll hash it and store it in the database and send the user a url with the non-
		//	hashed version. This will be their method of authenticating their account activation.
		$identifier = $this->randomlib->generateString(128);

		//	Update the appropriate fields in the database.
		$user = $this->user->create([
			'email' 		=> $email,
			'username' 		=> $username,
			'password'	 	=> $this->hash->password($password),
			'active' 		=> false,
			'active_hash' 	=> $this->hash->hash($identifier)
		]);
	
		//	Send the user an email with a link to activate their account. Link is generated in the template.
		$this->mailer->send('templates/email/registered.twig', ['user' => $user, 'identifier' => $identifier], function ($message) use ($user) {
			$message->to($user->email);
			$message->subject('Confirmation of registration.');
		});

		//	Redirect the user to the homepage.
		return $response->withRedirect($this->router->pathFor('home'));
	}

	//	Our validation failed, so render the registration page again, passing the errors and
	//	posted values. We use these to autofill certain fields if the user's input was valid
	//	so that they don't have to re-enter what they did correctly.
	return $this->view->render($response,'auth/register.twig', [
		'args' 		=> $args,
		'errors' 	=> $v->errors(),
		'posted' 	=> $posted
	]);
})->setName('register.post')->add($guest);