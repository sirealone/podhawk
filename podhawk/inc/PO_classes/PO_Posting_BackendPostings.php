<?php

class PO_Posting_BackendPostings extends PO_Posting_Extended

{
	private $trans = array(); // array of required data from translation table

	public function extendPostingData()
	{
		// get author nickname
		$this->posting['author_name'] = $this->getAuthorNickname();
		// count comments
		$comments = new PO_Posting_Comments($this->id);
		$this->posting['count_comments'] = $comments->getCount();
		// status
		$this->getStatus();
		// edit status
		$this->getEditStatus();
		// has audio, player type, media type
		$this->hasAudio();
	}
	
	public function getTranslationTable($trans)
	{
		foreach($trans as $name => $value)
		{
			$this->trans[$name] = $value;
		}
	}

	protected function getStatus()
	{
		switch ($this->posting['status'])
		{
			case 1:
				$this->posting['status_word'] = "<span style=\"color:#dd0067;\">".$this->trans['draft']."</span>";
				break;

			case 2:
				$this->posting['status_word'] = "<span style=\"color:#090;\">".$this->trans['finished']."</span>";
				break;

			case 3:
				$this->posting['status_word'] = $this->trans['onair'];
				break;
		}
	}

	protected function getEditStatus()
	{
		$user = new US_User($_SESSION['authorid']);
		$this->posting['may_edit'] = $user->mayEdit($this->posting['author_id']);
	}

	protected function hasAudio()
	{
		if (!empty($this->posting['audio_file']))
		{
			$this->posting['has_audio'] = true;

			if ($this->posting['filelocal'] == 1)
			{	
				$this->posting['audio_link'] = THIS_URL . "/audio/" . $this->posting['audio_file'];
			}
			else
			{
				$this->posting['audio_link'] = $this->posting['audio_file'];
			}

			$data = DataTables::AudioTypeData($this->posting['audio_type']);
			
			$this->posting['playertype'] = $this->getPlayerType(); 

			$this->posting['media_type'] = $this->getMediaTypeName();
		}
		else
		{
			$this->posting['has_audio'] = false;
		}	
	}
}
?>
