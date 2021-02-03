<?php

class RE_NewAddFile extends RE_CopyFTP
{

	public function makePosting()
	{
		$newFileName = $this->audioFileRename($this->file_name);
		$this->newFilePath = AUDIOPATH . $newFileName;

		try
		{
			$success = $this->copyFile();

			if (!$success)
			{
				$this->message = 'copy_fail';
				throw new Exception ("Failed to copy file {$this->file_name} to {$this->newFilePath}");
			}
	
			$dosql = "SELECT addfiles FROM " . DB_PREFIX . "lb_postings where id = :id";
			$array = array(':id' => $_GET['id']);
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$result = $GLOBALS['lbdata'] -> executePreparedStatement($array);

			$addfiles = (empty($result[0]['addfiles'])) ? array() : unserialize($result[0]['addfiles']);

			$addfiles[] = array('name' => $newFileName, // add new file to the array in 'addfiles'...
								'mime' => PO_Posting_Extended::getMimeType($this->getAudioType($newFileName))); // .. and find the appropriate mime type

			$serialised = serialize($addfiles);

			$dosql = "UPDATE " . DB_PREFIX . "lb_postings SET addfiles = :addfiles WHERE id = :id";
			$GLOBALS['lbdata'] -> prepareStatement($dosql);
			$array = array(':addfiles' => $serialised,
							':id' => $_GET['id']);
			$GLOBALS['lbdata'] ->executePreparedStatement($array);

			$message = "I have copied the file to the audio folder and attached it to this posting";
		}
		catch (Exception $e)
		{
			$this->log->write ($e->getMessage());
		}
	}

	protected function getAudioFileName()
	{		
		$this->file_name = $_POST['fileToAdd'];		
	}
}
?>
