<?php

class PO_New_NonAudio_Comment extends PO_New_Comment
{

	public function process()
	{
		if (isset($_POST['commentsubmit']))
		{			
			$this->commentVariables();

			$this->audio_file = '';
			$this->audio_type = '0';
			$this->audio_length = 0;
			$this->audio_size = 0;

			$this->checkSpam();

			if ($this->is_spam == false)
			{
				$success = $this->commentToDatabase();

			}
		}
	}
}
?>
