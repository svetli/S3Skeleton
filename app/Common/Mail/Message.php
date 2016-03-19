<?php
/**
*
*/

namespace App\Common\Mail;

class Message
{
	/**
	* @var PHPMailer $mailer 	A PHPMailer instance with our mail config.
	*/
	protected $mailer;
	
	/**
	* Constructor
	*
	* @param PHPMailer $mailer 	A PHPMailer instance with our mail config.
	*/
	public function __construct($mailer)
	{
		$this->mailer = $mailer;
	} //End constructor

	/**
	* Adds an address to the recipient list for the email.
	*
	* @param string $address 	An email address to send the message to.
	* @return null
	*/
	public function to($address)
	{
		$this->mailer->addAddress($address);
	} //End to

	/**
	* Sets the subject of the message.
	*
	* @param string $subject 	The subject of the email.
	* @return null
	*/
	public function subject($subject)
	{
		$this->mailer->Subject = $subject;
	} //End subject

	/**
	* Sets the body of the message.
	*
	* //Should be a param declaration here but I'm unsure if the twig template is rendered as a
	* //string or not, so I don't want to type hint it incorrectly. Either way, $body is the 
	* //rendered template of the email.
	* @return null
	*/
	public function body($body)
	{
		$this->mailer->Body = $body;
	} //End body
} //End class Message