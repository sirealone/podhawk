<?php

class PO_Posting_BackendRecord2 extends PO_Posting_Extended
{

	public function extendPostingData()
	{
		if (!empty($this->posting['audio_file']))
		{
			$this->posting['mediatypename'] = $this->getmediaTypeName();

			$this->posting['audio_link'] = $this->getUncountedAudioLink();

			$this->posting['playertype'] = $this->getPlayerType();

			$this->posting['audio_file_to_show'] = true;

			$this->posting['qtdata'] = $this->getQuicktime();

			$this->posting['amazonAvailable'] = $this->amazonAvailable();
			
		}
		else
		{
			$this->posting['audio_file_to_show'] = false;
		}
	}

	public function getEditor()
	{
		if (isset($_POST['editor_requested']) && $_POST['editor_requested'] != "")
		{
				$editor_to_use = $_POST['editor_requested'];
		}
		elseif ($this->posting['edited_with'] > 0)
		{
				$editor_to_use = $this->posting['edited_with'];
		}
		else
		{
				$editor_to_use = $this->reg->findSetting('markuphelp');
		}
		
		if ($editor_to_use == 2 || $editor_to_use == 3) // markdown and bbcode are deprecated
		{
			$editor_to_use = 5; // use 'raw html' instead
		}

		return $editor_to_use;
	}

	public function getCategories()
	{
		$return = array($this->posting['category1_id'], $this->posting['category2_id'], $this->posting['category3_id'], $this->posting['category4_id']);

		return $return;
	}

	private function isAmazon()
	{
		// is the audio file hosted in Amazon S3?
		$return = false;
		if ($this->posting['filelocal'] == false)
		{
			if (strpos($this->posting['audio_file'], 's3.amazonaws.com') !== false)
			{
				$return = true;
			}
		}
		return $return;
	}

	private function amazonAvailable()
	{
		if ($this->reg->amazonAvailable() == false)
		{
			return false;
		}
		elseif ($this->posting['filelocal'] == true)
		{
			return true;
		}
		elseif ($this->isAmazon() == true)
		{
			return true;
		}
	}
}
?>
