<?php
/**
*
*/

namespace App\Common\Mail;
use \App\Common\Mail\Message;

class Mailer
{
    /**
    * @var \Slim\Views\Twig $view 		The container's view dependency.
    * @var PHPMailer 		$mailer 	A PHPMailer instance with our mail config.
    */
    protected $view;
    protected $mailer;

    /**
    * Constructor
    *
    * @param \Slim\Views\Twig 	$view 	The container's view dependency.
    * @param PHPMailer 			$mailer A PHPMailer instance with our mail config.
    */
    public function __construct($view, $mailer)
    {
        $this->view = $view;
        $this->mailer = $mailer;
    } //End constructor

    /**
    * Grabs the appropriate template, creates a message body, and executes the
    * callback function before sending the email.
    *
    * @param string 	$template 	A view template for the email to be sent.
    * @param array  	$data 		Array of data to pass to the view.
    * @param function  	$callback 	The callback function that is executed after the message is created.
    *								Usually used to add details to the message such as the receiver & subject.
    * @return null
    */
    public function send($template, $data, $callback)
    {
        //	First create a new instance of our Message class passing our mailer object.
        $message = new Message($this->mailer);

        //	Now, send the template with the appropriate data to the message body.
        $message->body($this->view->fetch($template, $data));

        //	Execute our callback function.
        call_user_func($callback, $message);

        //	And send the message.
        $this->mailer->send();
    } //End send
} //End class mailer
