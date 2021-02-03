<?php

class LO_LogWriter
{
	private $logfile; // the log file to which we are writing
	private $logType; // the type of log (eg 'errors', 'events')
	private $week; // a six-digit string year + week
	private $logDirContents = array(); // array of the contents of the log directory
	private $weeksToKeep = 4; // how many weeks of compressed files to keep
	private $rotateFlag = TRUE; // should we try to rotate the log files?
	private $windows = false; // are we running on a windows machine?

	public function __construct($log)
	{
		$this->logType = $log; // eg 'errors', 'events'

		$this->week = $this->getWeek(); // "this week" = year + week number eg 201123

		$this->logfile = LOG_DIR . $log . $this->week . '.log';

		$this->windows = (strtoupper(substr(PHP_OS, 0, 3)) === "WIN");

	}

	public function write($text)
	{
		$time = date('Y m d H:i:s');

		$fp = @fopen($this->logfile, 'at');

		if ($fp)
		{
			fwrite($fp, "$time $text\n");	
			fclose($fp);
		}

		if ($this->rotateFlag == TRUE) // no need to try to rotate files more than once
		{
			$this->rotateLogFiles();
		}

		$this->rotateFlag = FALSE;
	}

	public function writeArray($array)
	{
		ob_start();
		print_r($array);
		$output = ob_get_contents();
		ob_end_clean();
		$this->write($output);
	}

	public function error($e, $errLoc)
	{
		$this->write("!!ERROR!! " . $e->getMessage());

		$this->write('The above error was thrown during execution of ' . $errLoc);

		if (DEBUG == 'verbose')
		{
			$this->writeArray($e->getTrace());
		}
	}

	public function destroy()
	{
		if (file_exists($this->logfile))
		{
			unlink ($this->logfile);
		}
	}

	private function writeToErrors($text) // for internal error messages
	{
		$time = date('Y m d H:i:s');

		$fp = @fopen(LOG_DIR . 'errors' . $this->week . '.log', 'at');

		if ($fp)
		{
			fwrite($fp, "$time $text\n");	
			fclose($fp);
		}
	}

	private function writeToEvents($text) // for internal events messages
	{
		$time = date('Y m d H:i:s');

		$fp = @fopen(LOG_DIR . 'events' . $this->week . '.log', 'at');
		
		if ($fp)
		{
			fwrite($fp, "$time $text\n");	
			fclose($fp);
		}
	}

	public function keepWeeks($weeks)
	{
		$this->weeksToKeep = $weeks;
	}

	private function rotateLogFiles()
	{
		$timestamp = strtotime("{$this->weeksToKeep} weeks ago"); // get timestamp for eg 4 weeks ago

		$weeksAgo = $this->getWeek($timestamp); // turn it into year+week format

		$unzippedFiles = $this->findLogs(array('log')); // find files with '.log' extension

		foreach ($unzippedFiles as $file)
		{
			if ($file['week'] < $this->week) // if there is an unzipped log file from an earlier week
			{
				$success = $this->zipFile($file['name']); // create a zipped file
				
				if ($success) // if we have created a zipped file, unlink the unzipped version
				{
					unlink (LOG_DIR . $file['name']);
				}
				else // if we cannot zip our log files...
				{
					if ($file['week'] <= $weeksAgo) // compare with 'week' for the file
					{
						unlink (LOG_DIR . $file['name']); // delete unzipped log files more than 4 weeks old
					}
				}
			}
		}

		$zippedFiles = $this->findLogs(array('zip', 'gz')); // find files with '.gz' or '.zip' extension
				
		foreach ($zippedFiles as $file)
		{
			if ($file['week'] <= $weeksAgo) // if the zipped file is more than 4 weeks old
			{
				unlink (LOG_DIR . $file['name']);
			}
		}
	}

	private function findLogs($extensions) // $extensions is an array of file extensions 
	{
		$logs = array();

		$logDirContents = get_dir_contents(LOG_DIR);

		foreach ($logDirContents as $file)
		{
			$bits = explode('.', $file);

			if (in_array(end($bits), $extensions)) // file extension is in the $extensions array
			{
				if (substr(reset($bits), 0, -6) == $this->logType) // first part of file name is $fileType
				{
					$week = substr(reset($bits), -6); // the 'week' part of the file name...
					{
						if (ctype_digit($week)) // ... is made up entirely of digits
						{
							$temp['name'] = $file;
							$temp['week'] = $week;
							$logs[] = $temp;
						}
					}
				}
			}
		}
		return $logs;
	}

	private function getWeek($timestamp='')
	{
		$timestamp = (!empty($timestamp)) ? $timestamp : time();

		$timestamp = (int)$timestamp; // make sure that $timestamp is an integer

		return (PHP_VERSION_ID > 50100) ? date("oW", $timestamp) : date("YW", $timestamp);
	}	

	private function zipFile($file)
	{
		try
		{
			if (extension_loaded('zlib') && defined('COMPRESSED_LOGS') && COMPRESSED_LOGS != 'zip')
			{
				$fh = fopen(LOG_DIR . $file, 'rb'); // open unzipped file for reading
				if (!$fh)
				{
					throw new Exception ("Unable to open $file for reading and compression");
				}

				$dest = LOG_DIR . $file . '.gz'; // name of file to write to
				$destfh = gzopen($dest, 'wb'); // open the file for (compressed) writing
				if (!$destfh)
				{
					throw new Exception ("Unable to open $dest for writing");
				}

				while (!feof($fh))
				{
					gzwrite($destfh, fread($fh, 1024)); // write in 1KB chunks from unzipped to zipped file
				}

				fclose($fh); // close files
				gzclose($destfh);
				return TRUE;			
			}
			elseif (extension_loaded('zip'))
			{
				$zip = new ZipArchive();

				$dest = LOG_DIR . $file . '.zip';

				if ($zip->open($dest, ZIPARCHIVE::CREATE)!==TRUE)
				{
					throw new Exception ("Unable to open $dest for writing");
				}

				$zip->addFile(LOG_DIR . $file, $file);

				$success = $zip->close();

				if (!$success)
				{
					throw new Exception ("Unable to add $file to zipped archive $dest");
				}
					
			}
			else
			{
				throw new Exception ("Unable to compress log file $file. The required PHP extension (zlib or zip) may not be loaded");
			}
		}
		catch (Exception $e)
		{	
			$this->writeToErrors($e->getMessage());
		}

		$this->rotateFlag = FALSE;
		$this->writeToEvents("Created compressed version of $file");
		return TRUE;
	}
	
	public function getLastLines($lines)
	{
		$bits = array();

		if (file_exists($this->logfile))
		{			
			if (!is_disabled('shell_exec') && $this->windows == false) // use UNIX shell command if possible
			{
				$file = escapeshellarg($this->logfile);
				$line = `tail -n $lines $file`;
				$bits = explode(PHP_EOL, $line);
			}
			else // read the file into an array - slower and uses more memory
			{ 
				$bits = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
				while (count($bits) > $lines)
				{
					array_shift($bits);
				}
			}
		}
		return $bits;
	}
			
}
?>
