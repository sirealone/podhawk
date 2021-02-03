<?php

class RE_NoAudio extends RE_NewAudioFile
{

	public function __construct($update_id)
	{
		parent::__construct($update_id);
	}

	public function makePosting()
	{
		if(empty($this->update_id))
		{
			$tempdate = date('Y-m-d H:i:s');

			$insertData = array(
				':author_id' 		=> $_SESSION['authorid'],				
				':title' 			=> 'New Posting',
				':posted' 			=> $tempdate,
				':message_input' 	=> '',
				':message_html' 	=> '',				
				':filelocal' 		=> 0,
				':audio_file' 		=> '',
				':audio_type' 		=> 0,
				':audio_size' 		=> 0,
				':audio_length' 	=> 0);
		
			$success = $this->insertDatabaseRow($insertData);

			$this->newPostId = $this->getNewPostId(false, $tempdate);
		}
		else
		{
			$updateData = array(
				':author_id' 		=> $_SESSION['authorid'],
				':filelocal' 		=> 0,
				':audio_file' 		=> '',
				':audio_type' 		=> 0,
				':audio_size' 		=> 0,
				':audio_length' 	=> 0,
				':id'				=> $this->update_id);
				
			$success = $this->updateDatabaseRow($updateData);
				
			$this->newPostId = $this->update_id;
		}

		$this->message = ($success) ? "plainsuccess" : "dbproblem";

	}

}
?>
