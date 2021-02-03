<?php

class LoginManager
{
 
	private $users;
	private $loginSuccess = false;
	private $encrypted = false;
	private $timeout = false;
	private $reg;
	private $log; // instance of LogWriter

	public function __construct ()
	{
		$this->reg = Registry::instance();

		$this->log = LO_EventLog::instance();
		
		$this->getUsersWithPasswords();

	}
	
	function makeChallenge ()
	{
		$challenge = generatePassword(16);
		$_SESSION['challenge'] = $challenge;
		return $challenge;
	}


	function validateChallenge ()
	{
		$id = false;
	
		if (!empty($_POST['challenge']))  //if encryption used
		{
			$this->encrypted = true;
			foreach ($this->users as $user)
			{
				if (md5($user['password'].$_SESSION['challenge']) == $_POST['challenge'] && $user['login_name'] == $_POST['login_name'])
				{
					$id = $user['id'];
					$this->loginSuccess = true;								
					break;
				}
			}
		
		}
		else //if no encryption
		{  
			$this->encrypted = false;
			foreach ($this->users as $user)
			{			
				if ($user['password'] == md5($_POST['password']) && $user['login_name'] == $_POST['login_name'])
				{
					$id = $user['id'];
					$this->loginSuccess = true;								
					break;
				}
			}
		} 
		return $id;
	}

	function logout()
	{
		//switch off the preview function
		$dosql = "UPDATE ".DB_PREFIX."lb_settings SET value  = '0' WHERE name = 'previews'";
		$GLOBALS['lbdata']->Execute($dosql);

	}

	function getReason ()
	{

		if ($this->loginSuccess == true)
		{	
			$reason = " logged in successfully";
		}
		else
		{
			$reason = " - unsuccessful login - unknown login name '".$_POST['login_name'] . "'";
			foreach ($this->users as $user)
			{
				if ($user['login_name'] == $_POST['login_name'])
				{
					$reason = " - unsuccessful login - wrong password";
					break;		
				}
			}
	
		}
		if ($this->encrypted == true) $reason .= " (encrypted)";
		return $reason;
	 }

	private function getUsersWithPasswords()
	{
		$users = $GLOBALS['lbdata']->getArray("SELECT id, login_name, nickname, password FROM ".DB_PREFIX."lb_authors;");
		//remove users for whom no password has been set
		foreach ($users as $i => $user)
		{
			if (empty($user['password']))
			{
				unset ($users[$i]);
			}
		}
		$this->users = $users;
	}

}
	
?>
