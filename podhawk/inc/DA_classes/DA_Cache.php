<?php

class DA_Cache
{
	private $cacheName;
	private $log;

	public function __construct($name)
	{
		$this->cacheName = $name;
		$this->log = LO_ErrorLog::instance();
	}

	public function writeToCache($data)
	{
		$serialized = serialize($data);

		if (is_writable(PH_CACHE))
		{
			file_put_contents(PH_CACHE . $this->cacheName, $serialized);
			return TRUE;
		}
		else
		{
			$this->log->write ("Unable to write data for '{$this->cacheName}' to PodHawk cache");
			return FALSE;
		}
		
	}

	public function getFromCache()
	{
		if (!is_readable(PH_CACHE . $this->cacheName))
		{
			 return FALSE;
		}

		$serialized = file_get_contents(PH_CACHE . $this->cacheName);
		
		$return = unserialize($serialized);
	
		return $return;	
		
	}

	public function deleteCache()
	{
		if (is_writable(PH_CACHE) && file_exists(PH_CACHE . $this->cacheName))
		{
			unlink (PH_CACHE . $this->cacheName);
		}
	}

	public static function deleteAll()
	{
		if (is_writable(PH_CACHE))
		{
			$files = get_dir_contents (PH_CACHE);
		
			foreach ($files as $file)
			{
				unlink (PH_CACHE . $file);
			}
		}
		else
		{
			$log = LO_ErrorLog::instance();
			$log->write ('Unable to delete the contents of' . PH_CACHE);
		}
	}	
}
?>
