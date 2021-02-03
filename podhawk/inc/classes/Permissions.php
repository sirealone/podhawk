<?php

class Permissions
{

	private $ftp_user;
	private $ftp_server;
	private $ftp_password;
	private $ftp_path;
	private $ftp_enabled;
	private $error;
	private $writable_dirs = array();
	private $lock = TIMTHUMB_CACHE;
	private $lock_life = 600; // 600 seconds = 10 minutes
	private $flags = array();
	private $windows; // is Podhawk running on a Windows machine?

	function __construct($writable_dirs)
	{	
		$reg = Registry::instance();

		$this->windows = (strtoupper(substr(PHP_OS, 0, 3) === 'WIN'));

		$this->ftp_enabled = (	USE_FTP_PERMISSIONS == true &&
								extension_loaded('ftp') &&
								$reg->ftpSettingsAvailable() == TRUE
							);
		
		if ($this->ftp_enabled)
		{
			$this->ftp_server = $reg->findSetting('ftp_server');
			
			// PodHawk root as seen by the ftp server, with trailing slash
			$this->ftp_path = substr($reg->findSetting('ftp_path'), 0, -6);

			$this->ftp_user = $reg->findSetting('ftp_user');
			$this->ftp_password = $reg->findSetting('ftp_pass');
		}

		// array of paths from PodHawk root directory of other directories which need to be writable
		$this->writable_dirs = $writable_dirs;
	}

	function __destruct()
	{
		if ($this->windows) return; // don't do anything on a Windows machine

		// before the Permissions object is destroyed, make sure that writable directories are closed for writing...
		foreach ($this->writable_dirs as $dir)  //$dir = path from Podhawk root
		{
			// Destroy any stray lock directories more than 10 minutes old...
			if ($this->old($this->get_lock_dir($dir)))
			{
				$this->unlink_lock_dir($dir);			
			}

			// Make not writable any writable directories - where we hold a flag (ie we made the dir writable, but the process ended 
			// before we could make it non-writable again); or where there is no lock directory (ie where the directory was left writable by
			// some other process more than 10 minutes ago)

			if (isset($this->lock_flag[$dir]) || !file_exists($this->get_lock_dir($dir)))
			{
				$this->make_not_writable($dir);
			}
		}		
	}


	public function make_writable($dir) // $dir = path from PodHawk root
	{
		$full_path = resolveDir($dir, PATH_TO_ROOT); //$full_path = path from server root

		$lock_dir = $this->get_lock_dir($dir); // full path to lock directory

		if (file_exists($full_path) && !$this->windows)
		{

			// if the dir already has 0777 permissions and there is a current lock-dir,
			// another user has already made the directory writable
			// we wait until he/she has reset the dir permissions in order to avoid a race condition

			while ($this->get_permissions($full_path) == "0777" &&  file_exists($lock_dir) && !$this->old($lock_dir))
			{
				sleep (1);
			}

			// if the file exists, and it is not writable, and we have the means to make it writable
			if (!is_writable($full_path) && $this->ftp_enabled)
			{ 		
				// make a lock directory
				$this->make_lock_dir($dir);

				if (file_exists($this->get_lock_dir($dir)))
				{
		
				}

				$path = resolveDir($dir, $this->ftp_path); //path from ftp root

				//connect to the ftp server
				$conn = $this->connect();
		
				if ($conn)
				{
					//change permissions to 0777 and disconnect
					$chmod = ftp_chmod($conn, 0777, $path);

					if ($chmod)
					{
						// set a flag to remind us that we have made the directory writable
						$this->lock_flag[$dir] = true;
					}
					$close = ftp_close($conn);

				}
				else
				{
					//if we can't connect (eg wrong username/password), remove the lock dir
					$this->unlink_lock_dir($dir);
				}		
			}
		}

		// return true if the dir is now writable, false if it isn't or if it does not exist.
		// Windows folders were checked as writable when PodHawk was installed.
		return is_writable($full_path);
	}


	public function make_not_writable($dir)  // $dir = path from PodHawk root
	{
		if ($this->windows) return true; // don't try to do anything on a Windows machine.

		$full_path = resolveDir($dir, PATH_TO_ROOT); //$full_path = path from server root

		//if the file exists and it has 0777 permissions and we have the means to make it non-writable
		if (file_exists($full_path) && $this->get_permissions($full_path) == "0777" && $this->ftp_enabled)
		{
	
			$path = resolveDir($dir, $this->ftp_path); //$path = path from ftp root

			$conn = $this->connect();
		
			if ($conn)
			{		
				$chmod = ftp_chmod($conn, 0755, $path);

				if ($chmod) unset ($this->lock_flag[$dir]);

				$close = ftp_close($conn);

				$this->unlink_lock_dir($dir);
			}

		}

		// return true if dir no longer has 0777 permissions
		// or false if it still has 0777 permissions or if it does not exist

		return (file_exists($full_path) && $this->get_permissions($full_path) != "0777");
	}

	private function make_lock_dir($dir)  // $dir = path from PodHawk root
	{
		mkdir ($this->get_lock_dir($dir), 0755);
	}

	private function unlink_lock_dir($dir)
	{ 
		if (file_exists($this->get_lock_dir($dir)))
		{
			rmdir ($this->get_lock_dir($dir));
		}
	}

	private function connect()
	{	
		$conn = ftp_connect($this->ftp_server);	
		$login = ftp_login($conn, $this->ftp_user, $this->ftp_password);

		if(!$conn || !$login) return false;

		return ($conn);
	}

	private function get_lock_dir ($dir)  // $dir = path from PodHawk root - no trailing slash!
	{
		$bits = explode(DIRECTORY_SEPARATOR, $dir);
		$name = end($bits); // find the part after the final directory separator, use this as the name

		$return = $this->lock . "lock_" . $name; 

		return $return;  // returns full path to lock dir
	}

	private function get_permissions ($path) //$path = path from root
	{
		return substr(sprintf('%o', fileperms($path)), -4);
	}

	private function old ($path)
	{
//returns true if the lock dir exists and is more than 10 minutes old, false if it is younger or if it does not exist
		if (!file_exists($path)) return false;
	
		return (time() > (filemtime($path) + $this->lock_life));
	}

## The following functions were used in debugging the class

public function make_evil_script()  {	

	$h = fopen (TIMTHUMB_CACHE . "evilscript.php", 'wb');
	$text = "<?php
echo \"Ha Ha Evil Script\";
?>";
	fwrite($h, $text);
	fclose($h);
		
	}

public function destroy_evil_script() {
	unlink (TIMTHUMB_CACHE . "evilscript.php");
	}

public function destroy_htaccess() {
	unlink (TIMTHUMB_CACHE . ".htaccess");
	}
public function test_time($dir)  {
	
	$lock_dir = $this->get_lock_dir($dir);
	echo "Lock dir made at " . filemtime($lock_dir);
	echo "<br />Time now " . time();
	sleep (5);
	echo "<br />Lock dir made at " . filemtime($lock_dir);
	echo "<br />Time now " . time();

	}
 
}
?>
