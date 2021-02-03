<?php

/**
*
* The DBBackup class is used to generate and send a compressed backup of the PodHawk database to the user.
*
*/

class MA_DBBackup extends MA_GeekMail
{

	protected $user_name;
	protected $user_email;
	protected $temp_file_location;
	protected $database = array();
		

	protected function getRecipientDetails()
	{
		$reg = Registry::instance();

		$author = $reg->getAuthor($_SESSION['authorid']);

		$this->user_name = (!empty($author['realname'])) ? $author['realname'] : $author['nickname'];
		$this->user_email = $author['mail'];

	}

	protected function makeBackup()
	{
		$this->temp_file_location = AUDIOPATH . DB_NAME . "_backup_" . date("Y-m-d") . ".gz";
		$command = "mysqldump --opt -u" . DB_USER . " -p" . DB_PASS . " " . DB_NAME . " | gzip > " .$this->temp_file_location;

		$result = system($command, $retvar);

		return (file_exists($this->temp_file_location) && filesize($this->temp_file_location) > 100);

	}

	protected function buildMessage()
	{
		$site_name = SITENAME;

		$message = "<p>Hi {$this->user_name}</p><p>A compressed backup file for the database at $site_name is attached.</p>";
	
		return $message;
	}

	public function sendBackup()
	{
		$this->getRecipientDetails();

		$success = $this->makeBackup();

		if ($success == false)
		{
			throw new Exception('Database backup failed');
		}

		$this->setMailType('html');

		$message = $this->buildMessage();

		$this->from('noreply@' . $this->getSenderDomain(THIS_URL), SITENAME);

		$this->to($this->user_email);

		$this->subject('Database Backup');

		$this->message($message);

		$this->attach($this->temp_file_location);

		$sent = $this->send();

		if ($sent == false)
		{
			$return = 'backup_email_not_sent';
		}
		else
		{
			$return = 'backup_email_sent';
		}
			
		return $return;

	}
}

?>
