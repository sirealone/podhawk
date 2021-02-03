<?

class Playlist   {
	private $name;
	private $dir = UPLOAD_PATH;
	private $xml = false;
	private $tracks = array();
	private $limit = 5;
	private $filename;
	private $oldFileName = '';
	private $permissions;
	private $allMP3 = true;
	private $log;
	private $reg;
	

	function __construct()
	{	
		// a default name in case the user does not create one	
		$this->name = "name" . generatePassword(6);

		$this->permissions = new Permissions(array('audio', 'upload'));
		$this->permissions->make_writable('audio');
		$this->permissions->make_writable('upload');

		$this->log = LO_EventLog::instance();

		$this->reg = Registry::instance();
	}
	
	function addIDArray ($array)
	{
		$this->filename = $this->filename('static');
		foreach ($array as $id)
		{
			$this->addID($id);
		}
		$this -> writeXSPF();
	}

	function addSSV ($string)
	{
		$bits = explode(" ", $string);
		$this -> addIDArray($bits);
	}

	function addTag($tag)
	{

		$this->filename = $this->filename('tag', $tag);
		$tag = escape(entity_encode($tag));

		$dosql = "SELECT * FROM ".DB_PREFIX."lb_postings WHERE tags LIKE '%" . $tag . "%' ORDER BY posted DESC LIMIT " .$this->limit. ";";
		$this->addTracks($dosql);

		$this -> writeXSPF();
	}

	function addCat($cat)
	{
		
		if (!ctype_digit(strval($cat))) return;

		$this->filename = $this->filename('cat', $cat);

		$where = ($cat == 0) ? "" : "WHERE category1_id = ".$cat." OR category2_id = ".$cat." OR category3_id = ".$cat." OR category4_id = ".$cat; 

		$dosql ="SELECT * FROM ".DB_PREFIX."lb_postings " . $where ." ORDER BY POSTED DESC LIMIT " .$this->limit. ";";
		$this->addTracks($dosql);

		$this -> writeXSPF();		

	}

	function setLimit ($value)
	{

		$this->limit = $value;

	}

	function setDir ($dir)
	{
	
		$this->dir = PATH_TO_ROOT . "/" . $dir ."/";

	}

	function setName($name)
	{
		if (!empty($name))
		{
			$this->name = $name;
		}
	}

	function getFileName()
	{

		return $this->filename;

	}

	function update()
	{	
		$this->dir = AUDIOPATH;
		$files = $this->findUpdateableXMLFiles();
		foreach ($files as $file)
		{

			$bits = explode("__", $file);

			$this->limit = $bits[3];
			$this->name = $bits[4];
			$this->oldFileName = $file;

			if ($bits[1] == 'cat')
			{						
				$this->addCat($bits[2]);
			}

			elseif ($bits[1] == 'tag')
			{				
				$this->addTag($bits[2]);
			}
		}
	}	
	
	function findPlaylistFiles($dir)
	{

		$xmlfiles = array();
		$this->dir = PATH_TO_ROOT . "/" . $dir . "/";
		$files = get_dir_contents($this->dir);

		foreach ($files as $file)
		{

			if ((substr($file,0,3) == "ud_" || substr($file,0,7) == "static_")  && substr($file, -4) == ".xml" && substr($file, -8) != ".rss.xml")
			{
			 	$data['filename'] = $file;
				$bits =  explode("__", $file);

				if (substr($file,0,3) == "ud_")
				{
					$data['name'] = $bits[4];

					if ($bits[1] == "cat")
					{
						if ($bits[2] == 0)
						{
							$data['type'] = "Category : all categories.<br /> Most recent " . $bits[3] ." postings.<br />Autoupdate : yes";
						}
						else
						{					
 							$data['type'] = "Category : " . $this->reg->getCategory($bits[2]) .".<br />Most recent " . $bits[3] ." postings.<br />Autoupdate : yes";
						}
					}

					elseif ($bits[1] == "tag")
					{		
						$data['type'] = "Tag : " . $bits[2] . ".<br />Most recent " . $bits[3] ." postings.<br />Autoupdate : yes";
					}
				}

				else
				{
					$data['name'] = $bits[1];
					$data['type'] = "Static XML file.<br />Autoupdate : no";
				}

				if ($this->dir == AUDIOPATH)
				{

					$dosql = "SELECT id FROM " . DB_PREFIX . "lb_postings WHERE audio_file = " . escape($file) .";";
					$result = $GLOBALS['lbdata']->GetArray($dosql);
					if ($result)
					{
						$data['id'] = $result[0]['id'];
					}
					else // we have a found a playlist which is not associated with a posting
					{
						$this->log->write("Found playlist file $file in audio directory. It does not appear to be associated with a posting. Consider deleting it.");

						continue;
					}
				}

				$xmlfiles[] = $data;
					
				}
		}

		return $xmlfiles;

	}
			
	private function addID ($id)
	{

		$id = trim($id);		
		if (!ctype_digit(strval($id))) return;
		$dosql = "SELECT * FROM ". DB_PREFIX . "lb_postings WHERE id = " . $id .";";
		$this->addTracks($dosql);		
		
	}

	private function filename ($a, $b="")
	{
		if ($a == 'static')
		{
			$filename = "static__" . $this->name . ".xml";
		}
		else
		{
			// we include a random component in the filename, to force browsers to reload the xml file when it is recompiled
			$random = generatePassword(6);
			$filename = "ud__" . $a . "__" .$b. "__" . $this->limit ."__" .$this->name. "__" . $random . ".xml";
		}

		return $filename;
	}	

	private function addTracks($dosql)
	{

		$result = $GLOBALS['lbdata'] -> GetArray($dosql);

		if (!empty($result))
		{		
			foreach ($result as $posting)
			{

				$p = new PO_Posting_PlaylistTrack($posting);

				$track = $p->getTrack();

				if ($track)
				{		
					$this->tracks[] = $track;
				}
			}
		}
	}
	
	private function date()
	{	
		$now = time();
		$tz = date("O", $now);  
		$tz = substr_replace ($tz, ':', 3, 0);
		return date("Y-m-d\TH:i:s", $now) . $tz;
	}

	private function image()
	{	
		if (file_exists(PATH_TO_ROOT . "/images/itunescover.jpg"))
		{
			return THIS_URL . "/images/itunescover.jpg";
		}
		else
		{
			return false;
		}
	}

	private function writeXSPF()
	{
		$location = $this->dir . $this->filename;

		$xspf = $this->buildXSPF();

		$fp = @fopen ($location, 'w');

		if (!$fp)
		{
			throw new Exception ('I cannot write playlist file ' . $this->filename);
		}

		fwrite($fp, $xspf);

    	fclose($fp);

		$this->log->write('Created playlist ' . $this->filename);
		
		//empty the $tracks array so that it does not pollute any other new XSPF file
		$this->tracks = array();

		if (!empty($this->oldFileName))
		{
			$dosql = "UPDATE " . DB_PREFIX . "lb_postings SET audio_file = :newaudiofile WHERE (filelocal = '1' AND audio_file = :oldaudiofile)";
			$GLOBALS['lbdata'] -> prepareStatement($dosql);
			$array = array(	':newaudiofile' => $this->filename,
							':oldaudiofile' => $this->oldFileName);
			$success = $GLOBALS['lbdata'] -> executePreparedStatement($array);

			if ($success)
			{
				$unlinkSuccess = @unlink($this->dir . $this->oldFileName);

				if (!$unlinkSuccess)
				{
					throw new Exception ('I have not been able to delete the old playlist file ' . $this->oldFileName . ' Please use an FTP programme to remove the file.');
				}

				$this->log->write('Deleted old playlist file ' . $this->oldFileName);
				
			}
			$this->oldFileName = '';
		}

	}

	private function buildXSPF()
	{
	
		if (class_exists('DOMDocument'))
		{
			$dom = new DOMDocument ('1.0');
			$dom->formatOutput = true;
			$dom->encoding = 'UTF-8';

			// playlist root
			$playlist = $dom->appendChild($dom->createElement('playlist'));
			$playlist->setAttribute('version', '1');
			$playlist->setAttribute('xmlns', 'http://xspf.org/ns/0/');
			$playlist->setAttribute('xmlns:jwplayer' , 'http://developer.longtailvideo.com/trac/wiki/FlashFormats');

			// title
			$title = $playlist->appendChild($dom->createElement('title'));
			$title->appendChild($dom->createTextNode($this->name));

			// creator
			$creator = $playlist->appendChild($dom->createElement('creator'));
			$value = my_html_entity_decode(SITENAME);
			$creator->appendChild($dom->createTextNode($value));

			// info
			$info = $playlist->appendChild($dom->createElement('info'));
			$info->appendChild($dom->createTextNode(THIS_URL));

			// image
			$image = $playlist->appendChild($dom->createElement('image'));
			$value = $this->image();
			$image->appendChild($dom->createTextNode($value));

			// date
			$date = $playlist->appendChild($dom->createElement('date'));
			$value = $this->date();
			$date->appendChild($dom->createTextNode($value));

			// extension - place to put stuff on updateability

			// tracklist
			$tracklist = $playlist->appendChild($dom->createElement('trackList'));

			// add individual tracks
			foreach ($this->tracks as $track)
			{
				$newTrack = $tracklist->appendChild($dom->createElement('track'));
			
				foreach ($track as $name => $value)
				{
					if ($name == 'provider') $name = 'jwplayer:provider';
					$value = my_html_entity_decode($value);
					$trackElement = $newTrack->appendChild($dom->createElement($name));
					$trackElement->appendChild($dom->createTextNode($value));
				}
			}
		
			$doc = $dom->saveXML();
//echo $doc;
//exit;

		}
		else // DOMDocument not available, so we build by hand and hope that the encoding issues will sort themselves out!
		{
			$doc = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\" xmlns:jwplayer=\"http://developer.longtailvideo.com/trac/wiki/FlashFormats\">\n";
			$doc .=	"<title>" . $this->name . "</title>\n";

		
			$siteName = DataTables::html_to_xml(SITENAME);

			$doc .=	"<creator>" . $siteName . "</creator>\n";

			$doc .=	"<info>" . THIS_URL . "</info>\n";
			$doc .= "<image>" . $this->image() . "</image>\n";
			$doc .=	"<date>" . $this->date() . "</date>\n\n";
			$doc .= "<trackList>\n\n";

		
			foreach ($this->tracks as $track)
			{
				$doc .= "<track>\n";

				foreach ($track as $name => $value)
				{
					if ($name == "provider") $name = "jwplayer:provider";
					
					$doc .= "\t<" . $name . ">" . $value . "</" . $name . ">\n";

				}

				$doc .= "</track>\n\n";
			}


			$doc .= "</trackList>\n";
			$doc .= "</playlist>";		
		}
		
		return $doc;	
	}

	private function findUpdateableXMLFiles()
	{
		$xmlfiles = array();
		$files = get_dir_contents(AUDIOPATH);
		foreach ($files as $file)
		{
			if (substr($file,0,3) == "ud_"  && substr($file, -4) == ".xml" && substr($file, -8) != ".rss.xml")
			{
			 	$xmlfiles[] = $file;
			}
		}

		return $xmlfiles;
			 
	}
}

?>
