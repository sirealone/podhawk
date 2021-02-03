<?php

class CacheManager
{
	private $cache_dirs = array();
	private $ftp_user;

	public function __construct($cache_dirs)
	{
		// array of full paths (from server root) of writable cache directories - with trailing slash
		$this->cache_dirs = $cache_dirs; 
	}

	public function __destruct()
	{
		//if the cache has been cleared the .htaccess file may have been removed also
		$this->make_htaccess_all();
	}

	public function make_directory ($full_path)  // $full_path = full path to cache directory
	{
		if (!file_exists($full_path) && is_writable(dirname($full_path)))
		{ 
			mkdir ($full_path, 0755);
			$this->make_htaccess ($full_path);
		}

	}

	public function make_directory_all ()
	{
		foreach ($this->cache_dirs as $full_path)
		{
			$this->make_directory($full_path);
		}
	}
		
	public function make_htaccess ($full_path) //$full_path = full path to cache directory with trailing slash
	{
		// if the SQLITE directory is 0777, then don't add an .htaccess 
		if ($full_path == SQLITE_DIR && $this->get_permissions(SQLITE_DIR) == '0777') return;

		if (defined('USE_HTACCESS') && USE_HTACCESS == FALSE) return;
	
		if (!file_exists($full_path . ".htaccess") && is_writable($full_path))
		{
			$h = fopen ($full_path . ".htaccess", 'wb');
			$text = "# Prevent access from outside web root
	order deny, allow
	deny from all";
			fwrite($h, $text);
			fclose ($h);
		}

	}

	public function make_htaccess_all()
	{
		foreach ($this->cache_dirs as $full_path) // $full_path = full path to cache directory with trailing slash
		{
			$this->make_htaccess($full_path);
		}
	}

	public function switch_cache_dir_perms ()
	{
		foreach ($this->cache_dirs as $dir)
		{
			if ($this->get_permissions($dir) == '0755') chmod($dir, 0777);
			else chmod($dir, 0755);
		}
	}

	public function open_cache_dirs()
	{
		foreach ($this->cache_dirs as $dir)
		{
			if ($this->get_permissions($dir) != '0777') chmod($dir, 0777);
		}
	}
			
	public function close_cache_dirs()
	{
		foreach ($this->cache_dirs as $dir)
		{
			if ($this->get_permissions($dir) != '0755') chmod($dir, 0755);
		}						
	}

	public static function get_permissions ($file) // $file = full path to file or dir
	{
		return substr(sprintf('%o', fileperms($file)), -4);
	}

}
?>
