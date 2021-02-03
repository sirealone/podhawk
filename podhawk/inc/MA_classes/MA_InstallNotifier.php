<?php

/*
*
* The InstallNotifier class sends an email to the owner of a newly installed PodHawk website
*
*/

 class MA_InstallNotifier extends MA_GeekMail {

	private $user_mail;
	private $user_name;
	private $user_url;

	public function __construct () {

	parent::__construct();

	}

	private function getRecipientDetails()  {
	
	$dosql = "SELECT nickname, mail FROM " . DB_PREFIX . "lb_authors WHERE id = '1'";
	$array = $GLOBALS['lbdata'] -> GetArray($dosql);

	$this->user_name = $array[0]['nickname'];
	$this->user_email = $array[0]['mail'];

	$dosql = "SELECT value FROM " . DB_PREFIX . "lb_settings WHERE name = 'url'";
	$array = $GLOBALS['lbdata'] -> GetArray($dosql);

	$this->user_url = $array[0]['value'];

	}
	private function buildMessage() {

	$message =<<<EOF

	<p>Hi {$this->user_name}</p>
	<p>Congratulations on your new PodHawk installation.</p>
	<p>If you have any problems or questions, look first at the PodHawk wiki at http://www.podhawk.com/docs or the PodHawk forum at http://www.podhawk.com/forum. Please post a question in the forum if you still have a problem, and the PodHawk developer and other users will try to help.<p>
	<p>This message was sent from your new PodHawk installation.</p>
EOF;
	return $message;
	
	}

	public function sendInstallMessage () {

	$this->getRecipientDetails();
	
	$this->setMailType('html');

	$message = $this->buildMessage();

	$this->from('noreply@' . $this->getSenderDomain($this->user_url), 'PodHawk');

	$this->to($this->user_email);

	$this->subject('Your New PodHawk Website');

	$this->message($message);

	$this->send();

	}
}
?>
