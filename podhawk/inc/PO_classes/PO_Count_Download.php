<?php

class PO_Count_Download
{
	private $log; // instance of LogWriter
	private $identifier; // eg 'j45.mp3'
	private $fileToSend; // the file we want to send
	private $method; // countfla, countpod or countweb - ie the counter we want to increment
	private $id; // the id of the posting
	private $filelocal; // is the file locally or remotely stored	
	
	public function __construct($request) 
	{
		$this->identifier = reset($request);

		$requestedMethod = key($request);
		
		$this->log = LO_ErrorLog::instance();

		$legalMethods = array('fla', 'pod', 'web');

		try
		{
			if(!in_array($requestedMethod, $legalMethods))
			{
				throw new Exception("Attempt to instantiate PO_Count_Download with illegal method $requestedMethod");
			}

			$this->method = 'count' . $requestedMethod;

			$this->fileToSend = $this->findFile($this->identifier);
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
		}
		
	}

	public function get()
	{
		if ($this->fileToSend)
		{
			$this->countDownload();

			$this->sendFile();
		}
	}

	private function findFile($identifier)
	{
		try
		{
			$bits 	= explode('.',$identifier); // split off file suffix
			$id 	= substr($bits[0],1); // remove 'j' to get the posting id

			if (!ctype_digit($id))
			{
				throw new Exception("PO_Count_Downloads cannot identify a posting with id $id");
			}
		
			$this->id = $id;

			$dosql = "SELECT audio_file, filelocal, addfiles FROM ".DB_PREFIX."lb_postings WHERE id = :id";
			$GLOBALS['lbdata'] -> prepareStatement($dosql);
			$result = $GLOBALS['lbdata'] -> executePreparedStatement(array(':id' => $id));

			$mainfile = isset($result[0]['audio_file']) ? $result[0]['audio_file'] : '';
			$addfiles = unserialize ($result[0]['addfiles']);
			
			// if the 'j' identifier has the same file extension as the audio_file named in the database, then we want that file..		
			if (getExtension($identifier) == getExtension($mainfile))
			{
				$audiofile = $mainfile; 
				$this->filelocal = isset($result[0]['filelocal']) ? $result[0]['filelocal'] : '';
			}
			// ...otherwise we search the 'addfiles' for one with the right extension
			else
			{
				foreach ($addfiles as $addfile)
				{
					if (getExtension($identifier) == getExtension($addfile['name']))
					{
						$audiofile = $addfile['name'];
						$this->filelocal = true;
						break;
					}
				}
			}
		
			if (empty($audiofile))
			{
				throw new Exception("PO_Count_Downloads cannot find an audio file associated with posting id $id");
			}
		
			return $audiofile;

		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());

			return false;
		}
		
	}

	private function countDownload()
	{
		// no counting if we have only a head request or if we have a range request
		if (strtolower($_SERVER['REQUEST_METHOD']) <> "head" && !isset($_SERVER['HTTP_RANGE']));
		{
			//do the counting action
			$dosql = "	UPDATE ".DB_PREFIX."lb_postings SET countall = countall + 1, 
						". $this->method . " = " . $this->method . " + 1 
						WHERE id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$GLOBALS['lbdata']->executePreparedStatement(array(':id' => $this->id));
		}

	}

	private function sendFile()
	{
		if ($this->filelocal == false)
		{
			// redirect to real location of externally-hosted file
			header("Location: ". $this->fileToSend, FALSE, 302);
		}
		else
		{
			if (SEND_DOWNLOAD_LOCATION_HEADERS == true)
			{
				PO_File_Sender::locationHeader($this->fileToSend);
			}
			else
			{
				// readthrough
				$sender = new PO_File_Sender(AUDIOPATH . $this->fileToSend);
				$sender->send();
			} 
		}
	}

}
?>
