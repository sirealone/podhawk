<?php

class PO_File_Sender
{
	private $fileToSend; // path from server root to the file to send
	private $fileNameToSend; // the filename that we want to declare in the headers
	private $ext; // the file extension
	private $log;
	private $mime; // mime-type of requested file

	public function __construct($file)
	{
		$this->log = LO_ErrorLog::instance();
		
		try
		{
			$this->ext = getExtension($file);

			$allowedExtensions = DataTables::getAudiotype('array_keys');

			if (!in_array($this->ext, $allowedExtensions))
			{
				throw new Exception("Attempt to instantiate PO_File_Sender to send file with illegal extension {$this->ext}");
			}
			
			if (is_readable($file))
			{
				$this->fileToSend = $file;
				$this->fileNameToSend = basename($file);
				$this->mime = $this->getMime();
			}
			else
			{
				throw new Exception("Cannot instantiate PO_File_Sender. File $file is not readable");
			}
		
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			$this->fileToSend = false;
		}
	}

	public function send()
	{
		try
		{
			if (headers_sent($filename, $linenum))
			{
				throw new Exception("Cannot send headers to download audio file {$this->fileToSend}. Headers already sent in $filename on line $linenum");	
			}
			elseif ($this->fileToSend)
			{
				if (isset($_SERVER['HTTP_RANGE']))
				{
					$this->sendRange();
				}
				else
				{
					$this->sendAll();
				}				
			}
			else
			{
				header ("HTTP/1.0 404 Not Found");
				throw new Exception("Unable to find requested file {$this->fileToSend}");
			}
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			die();
		}
	}

	private function sendAll()
	{
		$size = intval(sprintf("%u", filesize($this->fileToSend)));		

		$this->sendCommonHeaders();
		header("Content-Length: " . $size);
		
		//we do not send any data if we have a "head" request
		if (strtolower($_SERVER['REQUEST_METHOD']) != "head")
		{
			if (function_exists('apache_setenv') && !is_disabled('apache_setenv'))
			{
				@apache_setenv('no-gzip', 1);
			}
	  		@ini_set('zlib.output_compression', 0);	  	

			if (!is_disabled('set_time_limit'))
			{
				// Set the time limit based on an average D/L speed of 50kb/sec, with minimum of 1 second...
				$limit = ($size > 0) ? intval($size / 51200) + 60 : 1;

				//...and a maximum of 120 minutes
		  		set_time_limit(min(7200, $limit));
			}

			$memLimit = $this->return_bytes(ini_get('memory_limit'));

			// if file size is more than the PHP memory limit less 2MB, read it in chunks
			if ($size > ($memLimit - (1024 * 1024 * 2)))
			{

				$chunksize = 1 * (1024 * 1024); // how many megabytes to read at a time

				// Chunking file for download
				$handle = fopen($this->fileToSend, 'rb');
				$buffer = '';

				while (!feof($handle))
				{
					$buffer = fread($handle, $chunksize);
					echo $buffer;
					flush();
					@ob_flush();
				}
				fclose($handle);
			}
			else
			{
				// Streaming whole file for download
				@ob_clean();
				flush();
				$success = readfile($this->fileToSend);
				if (!$success)
				{
					throw new Exception ("Failed to download file {$this->fileToSend} using readfile()");
				}
			}
		} // end 'if not a head request'
		exit;		
	}
	
	private function sendRange()
	{
		$fp = @fopen($this->fileToSend, 'rb');
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
	
		// do we have a multiple range request?
		if (strpos($range, ',') !== false)
		{
			// if we have an audio comment, send all of it (to avoid revealing the file location,
			// and because its location may be inaccessible from the web)
			if (isset($_GET['com']) && substr($_GET['com'], 0, 1) == 'c')
			{
				$this->sendAll();
			}
			else // send a location header and let Apache sort the problem!
			{
				$filename = basename($this->fileToSend);
				$this->locationHeader($filename);
			}
		}
		else
		{
			$size = intval(sprintf("%u", filesize($this->fileToSend)));
			$end = $size-1;
			
			// '-500' = the last 500 bytes
			if (substr($range, 0, 1) == '-')
			{
				$c_start = $size - substr($range, 1);
				$c_end = $end;
			}
			else
			{
				$range  = explode('-', $range);
				$c_start = $range[0];
				$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $end;
			}
			// sanity check on the values of $c_start and $c_end
			if ($c_start > $c_end || $c_start > $size - 1 || $c_end > $end)
			{
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes 0-$end/$size");
				exit;
			}
	
			// the length of the bit we want to send
			$length = ($c_end - $c_start) +1;
	
			// move the pointer to the first byte we want to send
			fseek($fp, $c_start);
			
			// output some headers
			header('HTTP/1.1 206 Partial Content');
			header("Content-Range: bytes $c_start-$c_end/$size");
			header("Content-Length: $length");
			
			$this->sendCommonHeaders();

			// no output if we have a head request
			if (strtolower($_SERVER['REQUEST_METHOD']) != "head")
			{
				$buffer = 1024 * 8;
				while(!feof($fp) && ($p = ftell($fp)) <= $c_end)
				{
					if ($p + $buffer > $c_end)
					{
						// make sure we don't read past the end of the range
						$buffer = $c_end - $p + 1;
					}
					set_time_limit(0); // Reset time limit for big files
					echo fread($fp, $buffer);
					flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
				}

				fclose($fp);
			}
			exit;			
		}
	}
	
	public function setFileName($name)
	// we may want to declare a name for the file other than its name on the server
	{
		$this->fileNameToSend = $name;
	}

	private function return_bytes ($size_str)
	{
		switch (substr ($size_str, -1))
		{
		    case 'M': case 'm': return (int)$size_str * 1048576;
		    case 'K': case 'k': return (int)$size_str * 1024;
		    case 'G': case 'g': return (int)$size_str * 1073741824;
		    default: return $size_str;
		}
	}
	
	private function getMime()
	{
		$type = DataTables::getAudioType($this->ext);
		$typedata = DataTables::AudioTypeData($type);
		$mime = (!empty($typedata['mime'])) ? $typedata['mime'] : 'application/octet-stream';
		return $mime;
	}
	
	private function sendCommonHeaders()
	{
		$mtime = ($mtime = filemtime($this->fileToSend)) ? $mtime : gmtime();
		
		header("Content-Description: File Transfer");
		header('Content-Type: ' . $this->mime);
		header('Accept-Ranges: bytes');
		//header('Content-Transfer-Encoding: binary');

		if (isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false)
		{
			header("Content-Disposition: attachment; filename=" . urlencode($this->fileNameToSend) . '; modification-date="' . date('r', $mtime) . '";');
		}
		else
		{
			header("Content-Disposition: attachment; filename=\"" . $this->fileNameToSend . '"; modification-date="' . date('r', $mtime) . '";');
		}
	}
	
	static public function locationHeader($filename, $dir = 'audio')
	{
		$file = rawurlencode($filename);
		header ('Location: ' . THIS_URL . "/$dir/$file", FALSE, 302);
		exit;
	}
}

?>
