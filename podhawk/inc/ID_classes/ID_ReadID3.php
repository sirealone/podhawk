<?php

class ID_ReadID3
{
// a wrapper class for getID3

	private $reader; // getID3 object
	private $filedata = array(); // all ID3 etc info about the file

	public function __construct($file)
	{
		require_once (PATH_TO_ROOT . '/podhawk/id3/getid3.php');

		$this->reader = new getID3;

		$this->reader->encoding = 'UTF8';

		$this->fileData = $this->reader->analyze($file);

		getid3_lib::CopyTagsToComments($this->fileData);
	}

	public function getAllData()
	{
		return $this->fileData;
	}

	public function getDuration()
	{
		if (isset($this->fileData['playtime_string']))
		{
			return $this->fileData['playtime_string'];
		}
		else
		{
			return '0:00';
		}
	}

	public function getSeconds()
	{
		if (isset($this->fileData['playtime_seconds']))
		{
			return (int)$this->fileData['playtime_seconds'];
		}
		else
		{
			return 0;
		}
	}

	public function getSize()
	{
		if (isset($this->fileData['filesize']))
		{
			return $this->fileData['filesize'];
		}
		else
		{
			return 0;
		}
	}

	public function getBackendID3Data()
	{
	// data for the backend_id3 page
		$return = array(
			'title' 	=> $this->search('title'),
			'artist' 	=> $this->search('artist'),
			'album'		=> $this->search('album'),
			'year' 		=> $this->search('year'),
			'track' 	=> $this->search('track'),
			'genre' 	=> $this->search('genre'),
			'comment' 	=> $this->search('comments'),
			'image'		=> $this->getImage(),
			'imgtype'	=> $this->getImageExtension()
			);

		return $return;
	}

	public function getBackendRecord2Data()
	{
		// data needed for backend recording 2 page

		$bitrate 		= (isset($this->fileData['audio']['bitrate'])) ? $this->fileData['audio']['bitrate'] : '?';
		$bitrate_mode	= (isset($this->fileData['audio']['bitrate_mode'])) ? $this->fileData['audio']['bitrate_mode'] : '?';
		$sample_rate 	= (isset($this->fileData['audio']['sample_rate'])) ? $this->fileData['audio']['sample_rate'] : '?';
		$channelmode 	= (isset($this->fileData['audio']['channelmode'])) ? $this->fileData['audio']['channelmode'] : '';
		

		$return = array(
			'size' 			=> $this->getSize(),
			'duration' 		=> $this->getDuration(),
			'bitrate' 		=> $bitrate,
			'bitrate_mode' 	=> $bitrate_mode,
			'sample_rate' 	=> $sample_rate,
			'channelmode' 	=> $channelmode,
			'title' 		=> $this->search('title'),
			'track' 		=> $this->search('track')
			);

		return $return;
	}
	
	public function getInitialTitle($default='')
	{
	// attempts to create a title for a hew post from the id3 tags
		$title = $this->search('title');

		if (empty($title))
		{
			$title = $default;
		}
		
		$title = entity_encode(trim($title));
	
		return $title;
	}

	public function getInitialContent($default='')
	{
		// attempts to create contents for a new posting from the id3 tags
		$content = $this->getContent($default);

		$content = entity_encode(trim($content));

		return $content;
	}

	public function getInitialContentHTML($default='')
	{
		// initial content with some nice html tags
		$content = $this->getContent($default);

		$makeHTML = new HT_MakeHTML(1);

		$content = $makeHTML->make($content);

		return $content;
	}
		
	public function getHeight()
	{
		return (isset($this->fileData['video']['resolution_x'])) ? $this->fileData['video']['resolution_x'] : '';
	}

	public function getWidth()
	{
		return (isset($this->fileData['video']['resolution_y'])) ? $this->fileData['video']['resolution_y'] : '';
	}

	public function getImage()
	{
		return (isset($this->fileData['id3v2']['APIC'][0]['data'])) ? $this->fileData['id3v2']['APIC'][0]['data'] : '';
	}

	public function getImageMime()
	{
		$mime = '';

		if (isset($this->fileData['id3v2']['APIC'][0]['image_mime']))
		{
			$mime = $this->fileData['id3v2']['APIC'][0]['image_mime'];
		}
		elseif (isset($this->fileData['id3v2']['APIC'][0]['mime']))
		{
			$mime = $this->fileData['id3v2']['APIC'][0]['mime'];
		}
		
		return $mime;
	}

	public function getImageExtension()
	{
		$imgExtension = '';

		$mime = $this->getImageMime();
	
		$bits = explode('/', $mime);

		if (isset($bits[1]))
		{
			$imgExtension = '.' . $bits[1];
		}

		return $imgExtension;
	}			
		
	private function search($tag)
	{
		$alternatives = $this->alternativeTagNames($tag);

		$return = '';

		foreach ($alternatives as $alt)
		{
			if (isset($this->fileData['comments'][$alt][0]))
			{
				$return = $this->fileData['comments'][$alt][0];
			}
		}
		return $return;
	}
	
	private function alternativeTagNames($tag)
	{
		$array = array(	'year' 		=> array('year', 'date', 'creation_date'),
						'track' 	=> array('track', 'track_number', 'tracknumber'),
						'comments' 	=> array('comments', 'comment')
					);
		$return = (isset($array[$tag])) ? $array[$tag] : array($tag);
	
		return $return;
	}

	private function getContent($default='')
	{
		if (isset($fileinfo['tags']['id3v2']['unsynchronised_lyric'][0]))
		{
			$content = $fileinfo['tags']['id3v2']['unsynchronised_lyric'][0];
		}
		else
		{
			$content = $this->search('comments');
		}
		
		if (empty($content))
		{
			$content = $default;
		}
	
		return $content;
	}
}
?>
