<?php

class ID_WriteID3_DefaultTags extends ID_WriteID3
{
	protected $postTitle = '';

	public function setPostTitle($title)
	{
		$this->postTitle = $title;
	}

	protected function getDataToWrite()
	{
		$reg = Registry::instance();

		// have we got an image to attach to the mp3?
		$image = false;

		if (file_exists(AUDIOPATH . 'itunescover.jpg'))
		{
			$image = AUDIOPATH .'itunescover.jpg';
		}
		elseif (file_exists(IMAGES_PATH . 'itunescover.jpg'))
		{
			$image = IMAGES_PATH . 'itunescover.jpg';
		}
 
		$TagData['title'][] 	= $this->postTitle;
		$TagData['album'][] 	= $reg->findSetting('id3_album');
		$TagData['artist'][] 	= $reg->findSetting('id3_artist');
		$TagData['genre'][] 	= $reg->findSetting('id3_genre');
		$TagData['year'][] 		= date('Y');
		$TagData['comment'][] 	= $reg->findSetting('id3_comment');
		
		if ($image)
		{
			$fd = @fopen($image, 'rb');
			$APICdata = @fread($fd, filesize($image));
			@fclose ($fd);

			$TagData['attached_picture'][0]['data'] = $APICdata;
			$TagData['attached_picture'][0]['picturetypeid'] = '3'; 
			$TagData['attached_picture'][0]['description'] = SITENAME;
			$TagData['attached_picture'][0]['mime'] = 'image/jpeg';
		}

		$this->dataToWrite = $TagData;
	}
}
?>
