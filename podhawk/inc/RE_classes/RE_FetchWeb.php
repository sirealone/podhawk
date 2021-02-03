<?php

class RE_FetchWeb extends RE_NewAudioFile

{

	private $linkURL; // the URL from which we download the audio/kideo file
	private $newFilePath; // the path to the file in the Audio directory
	private $fileSize; // size of the downloaded file
	private $tempTitle; // a temporary title for the posting, derived from the file name

	public function __construct($update_id)
	{
		parent::__construct($update_id);
		
		if (!isset($_POST['linkurl']))
		{
			$this->message = 'noaudio';
			$this->newPostId = ($update_id) ? $update_id : '';
		}
		else
		{
			$this->linkURL = $this->addHttp($_POST['linkurl']);
			$this->file_name = $this->extractFileName($this->linkURL);
		}		
	}
	
	public function makePosting()
	{
		if (!empty($this->file_name)) // we can make a new posting only if we have a valid file name
		{
			$newFileName = $this->audioFileRename($this->file_name);

			$this->newFilePath = AUDIOPATH . $newFileName;

			$this->downloadFile();

			$this->setFilePermissions($this->newFilePath);		

			$filetype = $this->getAudioType($newFileName);

			$success = false;

			$id3 = new ID_ReadID3($this->newFilePath);

			if(empty($this->update_id)) // new post - we create a new row in the postings table
			{
				$this->tempTitle = $this->makeTempTitle();
			
				$insertData = array(
					':author_id' 		=> $_SESSION['authorid'],				
					':title' 			=> $id3->getInitialTitle($this->tempTitle),
					':posted' 			=> date("Y-m-d H:i:s"),
					':message_input' 	=> $id3->getInitialContent(),
					':message_html' 	=> $id3->getInitialContentHTML(),				
					':filelocal' 		=> 1,
					':audio_file' 		=> $newFileName,
					':audio_type' 		=> $filetype,
					':audio_size' 		=> $this->fileSize,
					':audio_length' 	=> $id3->getSeconds());
		
				$success = $this->insertDatabaseRow($insertData);

				$this->newPostId = $this->getNewPostId($newFileName, false);				
			}
			else // we update existing row, and delete the old audio file (if there is one)
			{
				$updateData = array(
					':author_id' 		=> $_SESSION['authorid'],
					':filelocal' 		=> 1,
					':audio_file' 		=> $newFileName,
					':audio_type' 		=> $filetype,
					':audio_size' 		=> $this->fileSize,
					':audio_length' 	=> $id3->getSeconds(),
					':id'				=> $this->update_id);
				
				$success = $this->updateDatabaseRow($updateData);
				
				$this->newPostId = $this->update_id;

			}

			$this->message = ($success) ? "linksuccess" : "dbproblem";

			if ($filetype == 1) // default tags for mp3s only
			{
				$postTitle = (isset($data['title'])) ? $data['title'] : $this->getTitleFromId($this->newPostId);
				$this->defaultID3Tags($postTitle);
			}
		}
	}

	private function downloadFile()
	{
		try
		{
			$sourcefile = fopen ($this->linkURL, "rb");
		
			if (!$sourcefile)
			{
				throw new Exception ('Unable to open the file at ' . $this->linkURL . ' for reading');
			}	

			if (!is_writable(AUDIOPATH))
			{
				throw new Exception ('The audio directory is not writable');
			}
		
			$destfile = fopen ($this->newFilePath, "wb");

			if(!$destfile)
			{
				throw new Exception('Unable to open the file at ' . $this->newFilePath . ' for writing');
			}

			$eof = false;
			$this->fileSize = 0;

			//copies the file in fragments of 1024 bytes
			do
			{
				$file = fread ($sourcefile, 1024) OR $eof = true;
				$this->fileSize = $this->fileSize + 1024;

				fwrite ($destfile, $file) OR fclose($destfile);
			}
			while ($eof==false);

			fclose($sourcefile);

			if ($this->fileSize == 0)
			{
				throw new Exception ("Unable to write from {$this->linkURL} to {$this->newFilePath}");
			}

			return true;
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			return false;
		}
	}

}
?>
