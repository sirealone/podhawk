<?php

class PO_Posting_Extended extends PO_Posting
{
	protected function getAuthorNickname()
	{
		return $this->reg->getNickname($this->posting['author_id']);
	}

	protected function getAuthorFullName()
	{
		$fullName = $this->reg->getRealName($this->posting['author_id']);
		if (empty($fullName))
		{
			$fullName = $this->getAuthorNickname();
		}
		return $fullName;
	}

	protected function getPlayerType()
	{
		$data = DataTables::AudioTypeData($this->posting['audio_type']);
	
		if (isset($data['player']))
		{
			return $data['player'];
		}
		else
		{
			return '';
		}
	}

	protected function getMediaTypeName()
	{
		$data = DataTables::AudioTypeData($this->posting['audio_type']);

		if (isset($data['display']))
		{
			return $data['display'];
		}
		else
		{
			return 'Media';
		}
	}

	protected function getIndirectAudioLink($dir) 
	{
		$return = '';

		// a link via 'get.php?...'
		$counting = $this->reg->findSetting('count' . $dir);

		if ($counting)
		{
			$filename = $this->makeFileNameFromID();
			$return = THIS_URL . "/get.php?" . $dir . "=" . $filename;    
		}
		else
		{
			$return = $this->getUncountedAudioLink();
		}

		if (veryempty($this->posting['audio_file']))
		{
			$return = "";
		}

		return $return;

	}

	protected function getDirectAudioLink($dir) // for links in RSS feed
	{
		// we divert the request via the non-existant 'fla/web/pod' directories to count downloads
		// - only works if there is a functioning .htaccess file in the root directory of the PodHawk installation 

		$countflash = $this->reg->findSetting('countfla');
		$countpod = $this->reg->findSetting('countpod');

		$filename = $this->makeFileNameFromID();

		if (veryempty($this->posting['audio_file'])) $audio = "";

		elseif ($countflash && $countpod)
		{
			$audio = THIS_URL . "/$dir/$filename";
		}
		else
		{

		//if Apache won't recognise the .htaccess file, turn off download counting for the feed ($countpod)
		//to get a direct link to the audio file. flv files in the jw player require an absolute, not relative, URL,
		//so we might as well provide one for all audio/video types
			$audio = $this->getUncountedAudioLink();
		}
		
		

		return $audio;
	}	

	protected function getPlayerLink()
	{
		$audio = "";

		$data = DataTables::AudioTypeData($this->posting['audio_type']);

		if (isset($data['countdownloads']))
		{			
			if ($data['countdownloads'] == 'getdir')
			{
				//flash audio players all seem to accept indirect links via the 'get'
				//directory, so we can use this method to count downloads

				$audio = $this->getIndirectAudioLink('fla');
			}
			elseif ($data['countdownloads'] == 'htaccess')
			{
				// get a link via the (fictional) 'fla' directory
				$audio = $this->getDirectAudioLink('fla');			
			}

			//however, YouTube won't play ball with our counting engine, so we have to link directly to the YouTube video;
			// and XML playlists cannot go through the counting engine (otherwise the counter will increment
			// on every page load!)
			else
			{
				$audio = $this->getUncountedAudioLink();
			}
		}
		return $audio;
	}

	protected function getUncountedAudioLink()
	{
		// a plain vanilla direct link to the file, not via the counting engine
		if ($this->posting['filelocal'])
		{
			$audio = THIS_URL . "/audio/" . rawurlencode($this->posting['audio_file']);
		}
		else
		{
			$audio = $this->posting['audio_file'];
		}
		return $audio;
	}

	public static function getPermalink($id=NULL)
	{

		if (empty($id) & isset($_GET['id'])) $id=$_GET['id'];

		try
		{

			// we want any exception to be rethrown and not handled by the normal DB exception handler
			$GLOBALS['lbdata'] ->exceptionHandlingMode('rethrow');

			$dosql = "SELECT permalink FROM " . DB_PREFIX . "lb_permalinks WHERE posting_id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $id));

			// return exception handling to default mode
			$GLOBALS['lbdata'] -> exceptionHandlingMode('default');		
	
			if (empty($result[0]))
			{
				throw new Exception();
			}
			
			$return = $result[0]['permalink'];
			
		}		
		catch (Exception $e)
		{
			$return = "index.php?id=$id";
			// return exception handling to default mode
			$GLOBALS['lbdata'] -> exceptionHandlingMode('default');	
		}

		return THIS_URL . '/' . $return;
	}

	public function getAssociatedLinks()
	{
		$dosql = "SELECT * FROM " . DB_PREFIX . "lb_links WHERE posting_id = :id";

		$GLOBALS['lbdata']->prepareStatement($dosql);

		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $this->id));
	
		return $result;	
	}

	public function getAssociatedCategories()
	{
		$catsdump = $this->reg->getCategoriesArray();
		$postingCats = $this->getCategoryarray();

		$data = array();

		foreach ($catsdump as $category)
		{
			if (in_array($category['id'], $postingCats))
			{
				$category['link'] = 'index.php?cat=' . $this->reg->getURLEncodedCategoryName($category['id']);
				$data[] = $category;
			}
		}
		
		return $data;
	}

	protected function makeFileNameFromID()
	{
		$bits = explode('.',basename($this->posting['audio_file']));

		foreach ($bits as $bit)
		{
			$ext = $bit;
		}
		$ext = (count($bits) == 1) ? '' : "." . $ext;

		$filename = "j" . $this->id . $ext;

		return $filename;
	}

	protected function getCategoryArray()
	{
		return array($this->posting['category1_id'],
					$this->posting['category2_id'],
					$this->posting['category3_id'],
					$this->posting['category4_id']);
	}

	protected function getImageSrc()
	{
		if (!empty($this->posting['image'])) // have we already linked an image with this posting?
		{
			$return = THIS_URL . '/images/' . $this->posting['image'];
			return $return;
		}
		else // look for the first image in the body of the posting
		{
			// returns the src attribute for the first image tag in a posting
			$regex = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';

			preg_match ($regex, $this->posting['message_html'], $matches);

			//always return an absolute address for the image
			if (!empty($matches[1]))
			{	
				$return = (substr($matches[1], 0, 7) == "http://" || substr($matches[1], 0, 8) == 'https://') ? $matches[1] : THIS_URL."/".$matches[1];
			
				// however, PodHawk may find a src attribute with arguments for use by timthumb - so strip out anything after jpg, jpeg, png or gif
				$timthumb_data_position = strpos($return, '&amp;');
				if ($timthumb_data_position)
				{
					$return = substr( $return, 0, $timthumb_data_position);
				}
				return $return;
			}

			else return false; // we cannot find an image
		}
	}

	protected function getQuicktime()
	// computes the parameters needed to place Quicktime player on the screen
	{
		if ($this->posting['playertype'] != 'qtaudio' && $this->posting['playertype'] != 'qtvideo')
		{
			return '';
		}
		else
		{
			$audiomov = THIS_URL . "/podhawk/backend/clicktoplayaudio.mov";
			$videomov = THIS_URL . "/podhawk/backend/clicktoplayvideo.mov";
			$height = 16;
			$width = 280;
			$target = "myself";
		
			if ($this->posting['playertype'] == 'qtaudio')			
			{
				$href = $audiomov;

				if ($this->posting['filelocal'] == true)
				{
					$id3 = new ID_ReadID3(AUDIOPATH . $this->posting['audio_file']);

					if ($id3->getWidth() != '')
					{
						$width 		= $id3->getWidth();
						$height  	= $id3->getHeight() + 16;
						$href 		= $videomov;
					}					
				}
			}
			elseif ($this->posting['playertype'] == 'qtvideo')
			{
				$href = $videomov;

				if ($this->posting['filelocal'] == true)
				{
					$id3 = new ID_ReadID3(AUDIOPATH . $this->posting['audio_file']);

					$width = $id3->getWidth();
					$height = $id3->getHeight();
				}
				else
				{
					 $target = "quicktimeplayer";
				}
			}

		return array(	'src'	=>$href,
						'width'	=>$width,
						'height'=>$height,
						'target'=>$target);
		}
	}

	public static function getMimeType($audio_type)
	{
		$data = DataTables::AudioTypeData($audio_type);
		return $data['mime'];
	}

	public static function getiTunesDuration($sec)
	{
		$hou = (int) ($sec / 3600);
    	$sec -= ($hou*3600);
    	$min = (int) ($sec / 60);
    	$sec -= ($min*60);

    	if ($hou < 10) { $hou = "0" . $hou; }
    	if ($min < 10) { $min = "0" . $min; }
    	if ($sec < 10) { $sec = "0" . $sec; }
    	return $hou.":".$min.":".$sec;
	}
}
?>
