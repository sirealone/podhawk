<?php

abstract class  DB_Wrapper
{
	protected $db;
	protected $log;
	protected $errLoc;
	protected $exceptionHandlingMode = 'default';

	abstract public function __construct($db_type);
	abstract public function Execute($dosql);
	abstract public function GetArray($dosql);
	abstract public function GetAssoc($dosql);
	abstract public function qstr($string);
	abstract public function prepareStatement($sql);
	abstract public function executePreparedStatement($array);

	protected function sqliteLocation()
	{
		if (file_exists(SQLITE_DIR . "loudblogdata.db") || ACTION == 'install')
		{
			return SQLITE_DIR . 'loudblogdata.db';
		}
		else
		{
			return AUDIOPATH . 'loudblogdata.db';
		}
	}
	protected function getConnectionParams()
	{
		if (defined('DB_USER')) // if there is a configuration file
		{
			// if the config file contains a front-end user name and password, and we want a webpage or the rss feed..
			if (defined('DB_FE_USER') && DB_FE_USER != '' && DB_FE_PASS != '' && ((ACTION == "webpage") OR (ACTION == "feed")))
			{
				$user = DB_FE_USER;
				$password = DB_FE_PASS;
			}
			else // ...else we use the main db user for everything
			{
				$user = DB_USER;
				$password = DB_PASS;
			}
			
			$host = DB_HOST;
			$name = DB_NAME;
		}
		elseif (ACTION == 'install') // we are installing PodHawk and have not yet created a configuration file
		{
			if (isset($_POST['sqluser'])) // if we are making a new PodHawk installation
			{
				$user = $_POST['sqluser']; 
				$password = $_POST['sqlpass'];
				$host = $_POST['sqlhost'];
				$name = $_POST['sqldata'];
			}
			else // we are converting from Loudblog using a Loudblog configuration file
			{
				global $db;
				$user = $db['user'];
				$password = $db['pass'];
				$host = $db['host'];
				$name = $db['data'];
			} 
		}

		$return  = array('user' => $user, 'password' => $password, 'host' => $host, 'name' => $name);
		return $return;
	}

	// how should we handle exceptions?
	public function exceptionHandlingMode($mode)
	{
		$this->exceptionHandlingMode = $mode;
	} 

	protected function DBExceptionHandler($e)
	{
		switch ($this->exceptionHandlingMode)
		{
			case 'rethrow' :		
			
				throw $e;
				break;
			
			case 'default' :
		 	default:
				$trace = $e->getTrace();

				$t = (isset($trace[1])) ? $trace[1] : $trace[0];

				$errorOrigin =  $t['file'] . ' line ' . $t['line'];

				$message = $e->getMessage();

				$newMessage = $message . '<br />' . 'Error origin ' . $errorOrigin;

				$this->log->error($e, $errorOrigin);

				// in backend pages, we can display the error as a message at the top of the page
				if (ACTION == 'backend' || ACTION == 'install')
				{
					throw new Exception($newMessage);
				}
				break;					

		}
		
	}	
}
?>
