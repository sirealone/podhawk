<?php

abstract class RE_NewAudioFile 
{
	protected $message = ''; // the message to display on the screen
	protected $reg;
	//protected (array); // array of data to put in the database
	protected $update_id = false; // the id of the post we are updating (false if we are not updating)
	protected $newPostId; // the id of our newly-created post
	protected $audio_type; // the PodHawk code for the type of audio/video etc file
	protected $file_name = ''; // filename with extension
	protected $temp_title = ''; // the temporary title of the new posting
	protected $log; // an instance of LogWriter
	protected $permissions; // an instance of the Permissions class
	protected $fileToDelete = ''; // when we are updating an existing post, the path to the old file which should be deleted 

	protected function __construct($update_id=false)
	// can be incorporated in  __construct methods of child classes
	// by calling parent::__construct();
	{
		$this->reg = Registry::instance();
	
		if ($update_id)
		{
			$this->update_id = $update_id;
		}

		$this->log = LO_ErrorLog::instance();
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getId()
	{
		return $this->newPostId;
	}

	abstract public function makePosting(); // there must be a 'controller' method which manages the process of creating the post


	protected function getAudioType($request)
	// reads the extension of an audio/video file and allocates the file to an audio_type
	{
		//we have an absolute path? okay, the file is the request
		if (strpos($request, "/"))
		{
			$file = $request;
		}
		else // the file is in the audio folder...
		{
			$file = AUDIOPATH . $request;
			
			// .. unless it isn't, in which case look in the upload folder
			if (!file_exists($file))
			{
				$file = UPLOAD_PATH . $request;
			}			
		}

		$extension = substr(strtolower(strrchr($request, ".")), 1);

		// get the audio type
		$audio_type = DataTables::getAudioType($extension);
	
		// identify enhanced podcasts
		if ($extension == 'm4a')
		{
			if (strpos($request, '://') !== false)
			{
				$audio_type == '14';
			}
			else
			{
				$id3 = new ID_ReadID3($file);

				$height = $id3->getHeight();

				if (!empty($height))
				{
					$audio_type = '14';
				}
			}
		}

		if (strpos($request, '://') === false) // local files only
		{
			if ($extension == 'm4b')
			{
				$id3 = new ID_ReadID3($file);

				$height = $id3->getHeight();

				if (!empty($height))
					{
						$audio_type = '14';
					}
			}

			// xml slideshow files
			if ($extension == 'xml')
			{
				$doc = new DOMDocument();
				$doc->load($file);
				$xmlroot = $doc->documentElement->nodeName;
				if ($xmlroot == 'slideshow')
				{
					$audio_type = '24';
				}
				elseif ($xmlroot == 'playlist')
				{
					$audio_type = '22';
				}
				else $audio_type = '19';
			}
		}
		return $audio_type;
	}

	protected function getFileName($file)
	{
		$name = basename($file);
		$bits = explode('.', $name);
		return $bits[0];
	}

	protected function insertDatabaseRow($data1)
	{

		$dosql = "INSERT INTO ".DB_PREFIX."lb_postings
			(author_id, title, posted, message_input, message_html, filelocal,  
			audio_file, audio_type, audio_size, audio_length, status, 
			countweb, countfla, countpod, countall, sticky, edited_with,
			jw_streamer, jw_streaming_file)
         	VALUES
         	(:author_id, :title, :posted, :message_input, :message_html, :filelocal,
			:audio_file, :audio_type, :audio_size, :audio_length, :status,
			:countweb, :countflash, :countpod, :countall, :sticky, :edited_with,
			:jw_streamer, :jw_streaming_file)";
		
    
		// if the following array items are not in $data1 array, we need to provide them
			$data2 = array (':status' => '1',
							':countweb' => '0',
							':countflash' => '0',
							':countpod' => '0',
							':countall' => '0',
							':sticky' => '0',
							':edited_with' => '0',
							':jw_streamer' => '',
							':jw_streaming_file' => '');

		$data = array_merge($data2, $data1);
		
		if (count($data) != 19)
		{
			throw new Exception('Submitted data about new posting does not contain the right number of fields.');
		}
	
		$GLOBALS['lbdata']->prepareStatement($dosql);
		$success = $GLOBALS['lbdata']->ExecutePreparedStatement($data);

		if (!$success)
		{
			throw new Exception ('Error in writing new row in postings table.');
		}
		return true;		
	}

	protected function UpdateDatabaseRow($data1) // we want to update an existing DB row
	{
		// find the old file, so that we can delete it when we have successfully update the DB
		$this->findFileToDelete($this->update_id);

		$dosql = "UPDATE ".DB_PREFIX."lb_postings SET

		author_id 			= :author_id,
		filelocal 			= :filelocal,  
		audio_file 			= :audio_file,
		audio_type 			= :audio_type,
		audio_length 		= :audio_length,
		audio_size 			= :audio_size,
		jw_streamer 		= :jw_streamer,
	 	jw_streaming_file 	= :jw_streaming_file 
         
         WHERE id = :id";		
		
		$data2 = array(
				':jw_streamer' 			=> '',
				':jw_streaming_file'	=> '');

		$data = array_merge($data2, $data1);
			
		if (count($data) != 9)
		{
			throw new Exception('Submitted data abou revised posting does not contain the right number of fields.');
		}

		$GLOBALS['lbdata']->prepareStatement($dosql);
		$success = $GLOBALS['lbdata']->executePreparedStatement($data);
			
		if($success)
		{
			$this->deleteUpdatedFile();
			return true;
		}
		else
		{
			unlink ($this->newFilePath);
			throw new Exception ('Unable to update database row. I have deleted the new audio file, and kept the old one');
		}
	
	}

	protected function findFileToDelete($id)
	{
		if ($id != false)
		{
			$dosql = "SELECT audio_file, filelocal FROM ".DB_PREFIX."lb_postings WHERE id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$row = $GLOBALS['lbdata']->executePreparedStatement(array(':id'=>$id));			

			if ($row[0]['filelocal'] == "1")
			{
				$this->fileToDelete = $row[0]['audio_file'];
			}
		}
	}
		
	protected function deleteUpdatedFile()
	{
		if (!empty($this->fileToDelete))
		{
			unlink (AUDIOPATH . $this->fileToDelete);
		}
	}

	
	protected function getNewPostId($file, $date)
	// finds the id which the database has given to a newly created posting
	// either from the date/time of the posting, or from the name of the audio file
	{
		if ($file)
		{	
			$dosql = "SELECT id FROM " . DB_PREFIX . "lb_postings WHERE audio_file = :file";
			$array = array(':file' => $file);	
		}

		if ($date)
		{
			$dosql = "SELECT id FROM ".DB_PREFIX."lb_postings WHERE posted = :posted";
			$array = array(':posted' => $date);
		}

		$GLOBALS['lbdata']->prepareStatement($dosql);
		$row = $GLOBALS['lbdata']->executePreparedStatement($array);
	
		if (empty($row[0]['id']))
		{
			throw new Exception ('Error. Cannot find the ID of the new posting.');
		}

		return $row[0]['id'];
	}

	protected function defaultID3Tags($postTitle)
	{			
		if ($this->reg->findSetting('id3_overwrite') == '1')
		{
			$tagWriter = new ID_WriteID3_DefaultTags($this->newFilePath);

			$tagWriter->setTagFormats(1);

			$tagWriter->setPostTitle($postTitle);

			if (!$tagWriter->writeData())
			{
				$this->message = 'tagerror';

				$this->log->write("Cannot write default id3 tags.");
				$errors = $tagWriter->getErrors();
				$errors = explode($errors, '<br />');
				$this->log->writeArray($errors);
			}
		}			
	}

	protected function addHttp($url)
	{
		if (substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")
		{	
			$url = "http://" . $url;
		}
		return $url;

	}

	protected function audioFileRename($file)
	{
		$newFileName = $this->tuneFileName($file);

		if($this->reg->findSetting('rename') == 1)
		{
			$tempAudioType = $this->getAudioType($file);
			$data = DataTables::AudioTypeData($tempAudioType);
			if (isset($data['rename']) && $data['rename'] !== false)
			{
				$suffix = strrchr($file, ".");

				$newFileName = $this->buildAudioName($suffix);
			}
		}
		return $newFileName;
	}

	protected function buildAudioName($suffix)
	{
		if (!empty($this->update_id))
		{
			$dosql= "SELECT posted FROM ".DB_PREFIX."lb_postings WHERE id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
        	$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id'=>$this->update_id));
			$date = $result[0]['posted'];
			$nameBody = substr($date,0,10);
		}
		else
		{
			$nameBody = date("Y-m-d");
		}

		if ((!isset($suffix)) OR (trim($suffix) == ""))
		{
			$suffix = ".mp3";
		}

		$prefix = $this->reg->findSetting('filename');
    	$daysec = 10000 + date("G")*3600 + date("i")*60 + date("s");
    	$filename = $prefix . "-" . $nameBody . "-" . $daysec . $suffix;

    	return $filename;
	}

	
	protected function tuneFileName($x)
	{
		// makes a file name browser- and sql-friendly
		$bad = array(" ", "'", '"', "(", ")", "‚Äô", ",", "?", "‚Äì", "#", "+", "&", "\\");
		$good = array("_", '', '', '', '', '', '', '', '-', '', '', '', '');
		$x = str_replace($bad, $good, $x); 

    	return $x;
	}

	protected function extractFileName($request)
	{
		$url = parse_url($request);

		if (isset($url['query'])) // if there is a query string, probably the file name is embedded in it
		{
			$extensions = DataTables::getAudioType('array_keys');
			$ext_string = implode('|',$extensions);
			//one or more alphanumeric chars followed by a recognised audio/visual extension
			$regex = "/\w+\.(" . $ext_string . ")/"; 

			if (preg_match($regex, $url['query'], $matches))
			{
				return $matches[0];
			}
		}

    	$path = $url['path']; // otherwise we look at the url path for the filename
    	$fragments = explode ("/", $path);
   		$i = 0;
    	while (isset($fragments[$i]))
		{
			$filename = $fragments[$i];
			$i += 1;
    	}
    	return $filename;
	}

	protected function setFilePermissions($filepath)
	{
		try
		{
			$success = chmod ($filepath, 0644);
			if (!$success)
			{
				throw new Exception("Unable to set new permissions for $filepath");
			}

		return true;
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			return false;
		}
	}

	protected function stripSuffix($filename)
	// removes extension from filenames if file is an audio/video file supported by PodHawk
	{
		$extensions = DataTables::getAudioType('array_keys');

		foreach ($extensions as $extension)
		{
			$replace[] = '.' . $extension;
		}
		
		$name = str_replace($replace, '', strtolower($filename));

		return $name;

	}

	protected function makeTempTitle ()
	// title from the file name, html encoded for insertion in DB as the title of the post
	{
		return $this->stripSuffix(htmlspecialchars($this->file_name), ENT_QUOTES);
	}
	
	protected function getTitleFromId($id)
	{
		$dosql = "SELECT title FROM " . DB_PREFIX . "lb_postings WHERE id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);		
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id'=>$id));
		return $result[0]['title'];
	}	
		
}
?>
