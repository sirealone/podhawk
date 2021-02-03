<?php

class ID_WriteID3
{

	protected $writer; // instance of getid3_writetags
	protected $file; // the path to the file we are writing to
	protected $audioType; // the audio_type code for the file we are writing to (1 = mp3, 3 = ogg etc)
	protected $APICData = false; // data for an image that we might want to attach to the file
	protected $imageMime = ''; // the mime type for $APICData
	protected $dataToWrite = array(); // an array of data to write to the file

	public function __construct($filename)
	{
		require_once (PATH_TO_ROOT . '/podhawk/id3/getid3.php');

		require_once(PATH_TO_ROOT . '/podhawk/id3/write.php');

		$this->writer = new getid3_writetags;

		$this->writer->filename = $filename;

		$this->writer->overwrite_tags = true;
 
		$this->writer->remove_other_tags = false;
 
		$this->writer->tag_encoding = 'UTF-8';
	
		$this->file = $filename;
	}

	public function setTagFormats($audioType)
	{
		$this->audioType = $audioType;

		$data = DataTables::AudioTypeData($audioType);

		if (isset($data['tags']))
		{
			$this->writer->tagformats = array($data['tags']);

			return true;
		}
		else
		{
			return false;
		}
	}

	public function writeData()
	{
		$this->getDataToWrite();

		$this->writer->tag_data = $this->dataToWrite;

		return ($this->writer->writeTags());
		
	}

	public function getErrors()
	{
		$errors = array_merge($this->writer->errors, $this->writer->warnings);

		return implode($errors, '<br />');
	}

	protected function getDataToWrite()
	{
		$TagData['title'][]  	= $_POST['id3title'];
		$TagData['artist'][]  	= $_POST['id3artist'];
		$TagData['album'][]  	= $_POST['id3album'];
		$TagData['track'][]  	= $_POST['id3track'];
		$TagData['genre'][]  	= $_POST['id3genre'];
		$TagData['year'][]  	= $_POST['id3year'];
		$TagData['comment'][]  	= $_POST['id3comment'];

		if ($this->audioType == 1) // attach image for mp3 files only
		{
			$this->getAPICData();

			if ($this->APICdata)
			{
				$TagData['attached_picture'][0]['data'] 			= $this->APICdata;
				$TagData['attached_picture'][0]['picturetypeid'] 	= '3'; 
				$TagData['attached_picture'][0]['description'] 		= SITENAME;
				$TagData['attached_picture'][0]['mime'] 			= $this->mime;
			}
		}
		
		$this->dataToWrite = $TagData;
	}

	protected function getAPICData()
	{
		// has the use selected an image from the images folder
		if (!empty($_POST['image_folder']) && file_exists(IMAGES_PATH . trim($_POST['image_folder'])))
		{
			$image 			= IMAGES_PATH . trim($_POST['image_folder']);

			$fd 			= @fopen($image, 'rb');

			$this->APICdata = @fread($fd, filesize($image));

			@fclose ($fd);

			$this->mime 	= "image/" . strtolower(getExtension($image));

		}

		//if not, has the user uploaded a file
		elseif (isset($_FILES['image']) && $_FILES['image']['size']<>"0")				
		{
			$image 			= $_FILES['image']['tmp_name'];
 
			$this->mime 	= $_FILES['image']['type'];

			$fd 			= @fopen($image, 'rb');

			$this->APICdata = @fread($fd, filesize($image));

			@fclose ($fd);
		}
				 
		//if not, use the old image
		else
		{
			$olddata 		= new ID_ReadID3($this->file);

			$this->APICdata = $olddata->getImage();

			$this->mime 	= $olddata->getImageMime();						
		}
					  
	}

}
?>
