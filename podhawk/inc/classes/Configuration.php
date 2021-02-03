<?php

// a class to find a valid configuration file, and create constants needed by the programme
class Configuration
{

	private $root;
	private $pathToConfig;
	private $configFileName;

	public function __construct()
	{
		$this->configFileName = 'config.php';

		$this->root = $this->findPodhawkRoot();
	}

	public function setPathToConfig($path) // $path = path from server root to the config file
	{
		$this->pathToConfig = $path;
	}

	public function configure()
	{
		$path_1 = $this->root . '/podhawk/custom/' . $this->configFileName; // standard location in 'custom' directory
		$path_2 = dirname($_SERVER['DOCUMENT_ROOT']) . '/' . $this->configFileName; // directory above document root
		
		$pathArray = array($this->pathToConfig, $path_1, $path_2);

		foreach ($pathArray as $path)
		{			
			$result = $this->findConfigFile($path);

			if ($result) break;
		}
		
		return $result;
	}

	private function findPodhawkRoot() // in case $_SERVER['DOCUMENT_ROOT'] is not set
	// NB it matters where this class file is located!!
	{
		$root = dirname(dirname(dirname(dirname(__FILE__))));

		return $root;
	}

	private function findConfigFile($path)
	{
		if (!empty($path) && @file_exists($path))
		{
			require_once ($path);
			
			if (defined('DB_TYPE')) // new style config file
			{
				return true;
			}
			elseif (isset($db)) // old style config file
			{
				$this->convertToConstants($db, $lb_path);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	private function convertToConstants($db, $lb_path)
	{
		define('DB_TYPE', $db['type']);
		define('DB_HOST', $db['host']);
		define('DB_NAME', $db['data']);
		define('DB_USER', $db['user']);
		define('DB_PASS', $db['pass']);
		define('DB_PREFIX', $db['pref']);
		if (isset($db['fe_user'])) define('DB_FE_USER', $db['fe_user']);
		if (isset($db['fe_pass'])) define('DB_FE_PASS', $db['fe_pass']);
		define('PATH_TO_ROOT', $lb_path);
	}
}
?>
