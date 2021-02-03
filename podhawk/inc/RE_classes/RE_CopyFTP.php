<?php

class RE_CopyFTP extends RE_NewAudioFile
{

	public function __construct($update_id)
	{
		parent::__construct($update_id);

		$this->getAudioFileName();
	}
		

	public function makePosting()
	{
		if (!empty($this->file_name))
		{
			$newFileName = $this->audioFileRename($this->file_name);
			$this->newFilePath = AUDIOPATH . $newFileName;

			try
			{
				$success = $this->copyFile();

				if (!$success)
				{
					$this->newPostId = ($this->update_id) ? $this->update_id : "";
					$this->message = 'copy_fail';
					throw new Exception ("Failed to copy file {$this->file_name} to {$this->newFilePath}");
				}
			
				$this->setFilePermissions($this->newFilePath);

				$filesize = filesize ($this->newFilePath);
				$filetype = $this->getAudioType($newFileName);

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
					':audio_size' 		=> $filesize,
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
					':audio_size' 		=> $filesize,
					':audio_length' 	=> $id3->getSeconds(),
					':id'				=> $this->update_id);
				
					$success = $this->updateDatabaseRow($updateData);
				
					$this->newPostId = $this->update_id;
				}				

				if ($filetype == 1) // default tags for mp3s only
				{
					$postTitle = (isset($data['title'])) ? $data['title'] : $this->getTitleFromId($this->newPostId);
					$this->defaultID3Tags($postTitle);
				}
			}
			catch (Exception $e)
			{
				$this->log->write($e->getMessage());
				$success = false;
			}

			$this->message = ($success) ? "copysuccess" : "dbproblem";		
		}
	}

	protected function getAudioFileName()
	{
		if (empty($_POST['filename']))
		{
			$this->message = 'noaudio';
			$this->newPostId = ($this->update_id) ? $this->update_id : '';
		}
		else
		{
			$this->file_name = $_POST['filename'];
		}		
	}

	protected function copyFile() // copy file from upload folder to the audio folder, and unlink the old file
	{
		$oldPath = UPLOAD_PATH . urldecode($this->file_name);
		$success = copy($oldPath, $this->newFilePath);
		if ($success)
		{
			unlink ($oldPath);
		}
		return $success;			
	}

}
?>
