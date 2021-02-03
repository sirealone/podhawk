<?php

abstract class PO_New_Comment
{
	protected $reg; // instance of Registry
	protected $log; // instance of LogWriter
	protected $posting_id;
	protected $is_spam = false;
	protected $author;
	protected $email;
	protected $website;
	protected $body;
	protected $permalink;
	protected $user_ip;
	protected $user_agent;
	protected $message_html;
	protected $posted;
	protected $audio_file;
	protected $audio_type = 0;
	protected $audio_length  = '0:00';
	protected $audio_size = 0;
	protected $clearCacheFlag = false;
	protected $warning = ''; // a warning if any of the posted data are too big for the database

	public function __construct($id)
	{
		$this->reg  = Registry::instance();

		$this->log = LO_ErrorLog::instance();	
	
		$this->posting_id = $id;

		$this->tunePosts();

	}

	protected abstract function process();

	public function clearCache()
	{
		return $this->clearCacheFlag;
	}

	public function getWarning()
	{
		return $this->warning;
	}

	private function tunePosts()
	{
		$_POST = array_map('trim', $_POST);

		if ($_POST['commentname'] == "")
		{
			$_POST['commentname'] = "Anonymous";
		}	
		
		if (substr($_POST['commentweb'],0,4) != "http")
		{
		    $_POST['commentweb'] = "http://".$_POST['commentweb'];
		}
	}

	private function checkPostSizes()
	{
		// some common-sense size limits
		$maxPostSizes = array(	'commentname' 		=> 30,
								'commentmail' 		=> 30,
								'commentweb' 		=> 30,
								'commentmessage' 	=> 1000
								);

		foreach ($maxPostSizes as $post => $size)
		{
			if (strlen($_POST[$post]) > $size)
			{
				$this->warning = "{$post}_too_big";
			}
		}

		if (!empty($_POST['commentmail']))
		{
			$regex = whitelist('email');
				
			if (!preg_match($regex, $_POST['commentmail']))
			{
				$this->warning = "invalid_email";
			}
		}

		if (!empty($_POST['commentweb']))
		{
			$regex = whitelist('web');

			if (!preg_match($regex, $_POST['commentweb']))
			{
				$this->warning = "invalid_url";
			}
		}

		if (empty($_POST['commentmessage']) && empty($_FILES['commentfile']) && empty($_SESSION['filethrough']))
		{
			$this->warning = "message_missing";
		}
	}

	protected function commentVariables()
	{
		$p = new HT_MakeCommentHTML;
		$message_html = $p->make($_POST['commentmessage']);

		$this->author 		= entity_encode($_POST['commentname']);
		$this->email 		= allowed_characters($_POST['commentmail'], 'email');
		$this->website 		= allowed_characters($_POST['commentweb'], 'web');
		$this->body 		= entity_encode($_POST['commentmessage']);
		$this->permalink 	= THIS_URL . "/index.php?id=" . $this->posting_id;
		$this->user_ip 		= $_SERVER['REMOTE_ADDR'];
		$this->user_agent 	= $_SERVER['HTTP_USER_AGENT'];
		$this->message_html = $message_html;
		$this->posted		= date('Y-m-d H:i:s');
	}

	protected function checkSpam()
	{
		if ($this->reg->findSetting('acceptcomments') == 'loudblog')
		{
			$this->is_spam = $this->loudblogCheck();
		}
		elseif ($this->reg->findSetting('acceptcomments') == 'akismet')
		{
			$this->is_spam = $this->akismetCheck();
		}
	}

	protected function loudblogCheck()
	{
		try
		{
			if (!empty($_POST['commentspam']))
			{
				$givenanswer = trim(strtolower($_POST['commentspam']));
				$rightanswer = trim(strtolower($this->reg->findSetting('spamanswer')));
				if ($givenanswer != $rightanswer)
				{
					throw new Exception('incorrect_antispam');
				}
			}
			else
			{
				throw new Exception ('missing_antispam');
			}
			return false; // comment is not spam
		}
		catch (Exception $e)
		{
			$this->warning = $e->getMessage();
			$this->log->write('A user failed to answer anti-spam question correctly. Comment rejected as spam.');
			return true; // comment is spam
		}			
	}
	
	protected function akismetCheck()
	{
		require PATH_TO_ROOT . "/podhawk/lib/akismet.class.php";

		$akismet_array = array(	'author' 		=> $this->author,
								'email' 		=> $this->email,
								'website' 		=> $this->website,
								'body' 			=> $this->body,
								'permalink' 	=> $this->permalink,
								'user_ip' 		=> $this->user_ip,
								'user_agent' 	=> $this->user_agent);

		$akismet = new Akismet(THIS_URL, $this->reg->findSetting('akismet_key'), $akismet_array);

		if ($akismet->errorsExist())
		{
	 		$errors = $akismet->getErrors();
			$this->log->write("There are Akismet errors.");
			foreach ($errors as $error)
			{
				$this->log->write($error);		
			}
		}
		elseif ($akismet->isSpam())
		{					
			if ($this->reg->findSetting('keep_spam') == true)
			{
					$this->writeToSpam($akismet_array);			
			}

			$this->warning = "spam";

			return true;
		}
		else
		{
			return false;
		}
	}

	protected function writeToSpam($array)
	{
		foreach ($array as $key => $value)
		{
			$prepared_statement_array[':' . $key] = $value;
		}

		$prepared_statement_array[':message_html'] = $this->message_HTML;
		$prepared_statement_array[':posting_id'] = $this->posting_id;

		$prepared_statement_array[':posted'] = date("Y-m-d H:i:s");

		$dosql = "INSERT INTO " . DB_PREFIX . "lb_spam (author, email, website, body, permalink, user_ip, user_agent, message_html, posted, posting_id) VALUES :author, :email, :website, :body, :permalink, :user_ip, :user_agent, :message_html, :posted, ':posting_id'";

		$_GLOBALS['lbdata']->prepareStatement($dosql);
		$_GLOBALS['lbdata']->executePreparedStatement($prepared_statement_array);
	}
	
	protected function commentToDatabase()
	{
		// do some basic sanity checks on the submitted data
		$this->checkPostSizes();

		// if the checks have not generated a warning, send data to database
		if (empty($this->warning))
		{
			try
			{
				$prepared_statement_array = array( 	':posting_id' 	=> $this->posting_id,
													':posted' 		=> $this->posted,
													':author' 		=> $this->author,
													':email' 		=> $this->email,
													':website' 		=> $this->website,
													':user_ip' 		=> $this->user_ip,
													':body' 		=> $this->body,
													':message_html' => $this->message_html,
													':audio_file' 	=> $this->audio_file,
													':audio_type' 	=> $this->audio_type,
													':audio_length' => $this->audio_length,
													':audio_size' 	=> $this->audio_size);

				$dosql = "INSERT INTO " . DB_PREFIX . "lb_comments
		(posting_id, posted, name, mail, web, ip, message_input, message_html, audio_file, audio_type, audio_length, audio_size) VALUES (:posting_id, :posted, :author, :email, :website, :user_ip, :body, :message_html,
				:audio_file, :audio_type, :audio_length, :audio_size)";
	

				$GLOBALS['lbdata']->prepareStatement($dosql);
	
				$GLOBALS['lbdata']->executePreparedStatement($prepared_statement_array);

				$this->finalActions();
			}
			catch (Exception $e)
			{
				$this->log->write('Error sending new comment data to database.');
				$this->log->write($e->getMessage());
				return false;
			}
		}
	}

	protected function finalActions() // do these things if comment successfully sent to DB
	{
		$this->sendCommentEmail();

		$this->clearCacheFlag = true;

		// prevent possible comment resubmission if user refreshes the page
		header ("Location: " . THIS_URL . "?id=" . $this->posting_id);
	}

	protected function sendCommentEmail()
	{
		$mailer = new MA_CommentNotifier($this->posting_id, $this->author, $this->email, $this->website, $this->message_html);

		$mailer->sendCommentEmail();
	}

}
?>
