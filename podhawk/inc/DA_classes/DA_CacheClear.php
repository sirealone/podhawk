<?php

class DA_CacheClear
{
	private static $instance; // because this is a singleton class
	private $reg; // instance of Registry
	private $flags = array(); // array of flags for different cache clearance methods
	private $eventLog;
	private $errorLog;
	
	private function __construct()
	{
		$this->reg = Registry::instance();
		$this->eventLog = LO_EventLog::instance();
		$this->errorLog = LO_ErrorLog::instance();
	}

	private function __clone()
	{

	}

	public static function instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	// it is often not sensible to try to clear cache directories mid-way through a run.
	// so we collect 'flags' for the caches to be cleared and clear them at the end
	public function setFlag ($flagArray) 
	{
		foreach ($flagArray as $flag)
		{
			if ($flag == 'Registry') // but we can refresh the Registry now
			{
				$this->reg->refreshAll();
			}
			else
			{
				$this->flags[] = $flag;
			}
		}
	}

	public function clearCaches()
	{
		foreach ($this->flags as $flag)
		{
			switch ($flag)
			{		
				case 'SmartyCache':
					$dirToClear = SMARTY_CACHE_DIR;
					break;
				case 'SmartyCompiledTemplates':
					$dirToClear = SMARTY_COMPILED_TEMPLATES_CACHE;
					break;
				case 'PHCache':
					$dirToClear = PH_CACHE;
					$this->reg->blockCaching(); // prevents the Registry from caching itself at the end of the run
					break;
				case 'TimThumbCache':
					$dirToClear = TIMTHUMB_CACHE;
					break;
				case 'HTMLPurifierCache':
					$dirToClear = HTML_PURIFIER_CACHE;
			}
			if (isset($dirToClear))
			{
				$this->clearDir($dirToClear);
			}
			else
			{
				$this->errorLog->write("Unknown clear directory request " . $flag);
			}
		}
	}

	private function clearDir ($dir) // $dir = path to an arbitrary directory from server root (with trailing slash)
	{
		if (is_writable($dir))
		{
			$files = get_dir_contents ($dir);
			foreach ($files as $file)
			{
				if (is_dir($dir . $file))
				{
					$this->clearDir($dir . $file . DIRECTORY_SEPARATOR);
				}
				elseif ($file != ".htaccess") // lets not delete any .htaccess files
				{
					unlink ($dir . $file);
				}
			}
			$this->eventLog->write("Cleared cache directory " . $dir);			
		}
		else
		{
				$this->errorLog->write("Unable to clear cache directory " . $dir);
		}
	}
}
?>
