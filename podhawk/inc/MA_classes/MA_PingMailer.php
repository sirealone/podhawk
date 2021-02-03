<?php

/**
*
* the Ping Mailer class sends emails when a new posting is first put on air
*
* The constructor takes the argument $args, being an array of the recipient(s), the sender and the text.
*
*/

class MA_PingMailer extends MA_GeekMail  {

	
	public function __construct()  {

		parent::__construct();

		}

	public function send_emails($args) {

	$this->setMailType('html');

	$this->from('noreply@' . $this->getSenderDomain(THIS_URL), SITENAME);

	$this->to($args['to']);

	$this->subject('New Posting on ' . SITENAME);

	$this->message($args['message']);

	$sent = $this->send();

	return $sent;

	}	

}
?>
