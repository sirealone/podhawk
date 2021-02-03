<?php

class SessionMaker
{

	var $user_agent;
	var $systemAuthKey;
	var $sessionAuthKey;
	var $life;
	var $usePageAuthentication;
	var $useDatabase;
		

	function __construct()
	{
	
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];

		$this->systemAuthKey = (defined('PAGE_AUTH_KEY')) ? PAGE_AUTH_KEY : '';

		$this->usePageAuthentication = (defined('AUTHENTICATE_BACKEND_REQUESTS') && AUTHENTICATE_BACKEND_REQUESTS == true);

		$this->useDatabase = (defined('STORE_SESSIONS_IN_DATABASE') && STORE_SESSIONS_IN_DATABASE == true && PH_VERSION > 1.4);
	}

	function start ()
	{
	
		if ($this->useDatabase)
		{

			session_set_save_handler(	array($this,'_open'),
				 						array($this,'_close'),
				 						array($this,'_read'),
				 						array($this,'_write'),
				 						array($this,'_destroy'),
										array($this,'_garbage'));

			$this->life = (defined('SESSION_LIFE')) ? SESSION_LIFE : 1200;

			//Debian systems will often have gc_probability set to 0
			if (ini_get('session.gc_probability') == 0) ini_set('session.gc_probability', 1);

		}
		else
		{

			$this->checkSavePath();

		}

		ini_set('session.use_only_cookies', 1);

		session_start();
	
		if (!isset($_SESSION['session_auth_key']))
		{
		
			$this->createSessionAuthKey();
	
		}
		
	}

	function setVariables($id)
	{
	
		//on successful login, we generate a new session id and a new authorisation key
		session_regenerate_id();
		$this->createSessionAuthKey();
		$_SESSION['authorid'] = $id;
		$_SESSION['fingerprint'] = $this->user_agent;
		unset($_SESSION['challenge']);

	}

	function destroy()
	{
	
		session_unset();
		@session_destroy();
	
	}

	function validate()
	{

		return $this->user_agent == $_SESSION['fingerprint'];

	}

	function checkSavePath()
	{

		if (!is_writable(session_save_path()))
		{

			if (PH_VERSION == 1.4) //users upgrading from 1.4 will need the standard session save path for their first login.
			{ 
				session_save_path(AUDIOPATH);
			}
			else
			{
	 			die ("I cannot write session data into the session save path ".session_save_path()." directory.
					 Please ask your web host or server administrator to change the setting for session.save_path to a writeable directory.");
			}
		}
	}

	function createSessionAuthKey()
	{
	
		$this->sessionAuthKey = generatePassword(16);
		$_SESSION['session_auth_key'] = $this->sessionAuthKey;

	}

	function createPageAuthenticator($page)
	{
	
		return md5($_SESSION['session_auth_key'] . $this->systemAuthKey . $page);

	}

	function validatePage($validation_string)
	{

		return $validation_string == md5($_SESSION['session_auth_key'] . $this->systemAuthKey . $_GET['page']);

	}

	function authenticate()
	{
	
		if($this->usePageAuthentication == false) return true;

		if (isset($_POST['auth'])) $validation_string = $_POST['auth'];
		elseif (isset($_GET['auth'])) $validation_string = $_GET['auth'];
		else return false;
	
		return ($this->validatePage($validation_string));

	}

	function write()
	{
	
		if ($this->useDatabase)
		{  
			@session_write_close();
		}
	}

// functions needed for storing sessions in database
	
	function _open($save_path, $session_name)
	{
		return true;
	}

	function _close()
	{
		return true;
	}

	function _read ($identifier)
	{

		$timeout = time()-$this->life;
		$dosql = "DELETE FROM ".DB_PREFIX."lb_sessions WHERE time < ".$timeout.";";
		$GLOBALS['lbdata']->Execute($dosql);
	
		$dosql = "SELECT session_data FROM ".DB_PREFIX."lb_sessions WHERE identifier = ".escape($identifier).";";
		$result = $GLOBALS['lbdata']->GetArray($dosql);
		if (empty($result[0]['session_data'])) return false;
		return $result[0]['session_data'];
	}

	function _write($identifier, $session_data)
	{

		$time = time();

		if (DB_TYPE == 'mysql')
		{

			$dosql = "REPLACE INTO ".DB_PREFIX."lb_sessions VALUES (".escape($identifier).", '".$time."', ".escape($session_data).");";
			return $GLOBALS['lbdata']->Execute($dosql);
		
		}
		else
		{
			//REPLACE is mysql-specific
			$dosql = "DELETE FROM ".DB_PREFIX."lb_sessions WHERE identifier = ".escape($identifier).";";
			$GLOBALS['lbdata']->Execute($dosql);

			$dosql = "INSERT INTO ".DB_PREFIX."lb_sessions VALUES (".escape($identifier).", '".$time."', ".escape($session_data).");";
			return $GLOBALS['lbdata']->Execute($dosql);

		} 


	}

	function _destroy($identifier)
	{

		$dosql = "DELETE FROM ".DB_PREFIX."lb_sessions WHERE identifier = ".escape($identifier).";";
		return ($GLOBALS['lbdata']->Execute($dosql));

	}

	function _garbage($max)
	{

		//probably not needed, since we clear out-of-date sessions before reading, but just in case...
		$timeout = time() - $max;

		$dosql = "DELETE FROM ".DB_PREFIX."lb_sessions WHERE time < ".$timeout.";";
		return $GLOBALS['lbdata']->Execute($dosql);

	}

}

?>
