<?php

class PO_Posting_PlaylistTrack extends PO_Posting_Extended
{

	private $track = array();

	public function getTrack()
	{
		$this->buildTrack();
		return $this->track;
	}

	private function buildTrack ()
	{		
		if ($this->checkType())
		{
			$track['location'] 	= $this->getPlayerLink();
			$track['title'] 	= DataTables::html_to_xml($this->posting['title']);
			$track['info'] 		= $this->getPermalink($this->id);

			if ($this->getImageSrc())
			{
				$track['image'] = $this->getImageSrc();
			}
			
			if ($this->getProvider())
			{
				$track['provider'] = $this->getProvider();
			}

			//JW Player 5.2 does not like duration = 0
			if (!empty($this->posting['audio_length']))
			{
				$track['duration'] = $this->posting['audio_length'];
			}

			$track['annotation'] = substr(DataTables::html_to_xml(strip_tags($this->posting['message_html'])), 0, 50) . "...";

			$this->track = $track;
		}
		else
		{
			$this->track = false;
		}
	}

	protected function getPlayerLink()
	{
		// it is best to use links via 'fla' in JW player playlists, even for mp3s
		// YouTube will not play nicely wih the counting engine so we need a direct link
		if ($this->posting['audio_type'] == 21)
		{
			$audio = $this->posting['audio_file'];
		}
		else
		{
			$audio = $this->getDirectAudioLink('fla');
		}

		return $audio;
	}

	private function checkType()
	{
		$allowed_types = array(1, 10, 20, 21);

		return (in_array($this->posting['audio_type'], $allowed_types));

	}
		
	private function getProvider()
	{
		$data = DataTables::AudioTypeData($this->posting['audio_type']);
		if (!empty($data['provider']))
		{
			return $data['provider'];
		}
		else
		{
			return false;
		}
	}
}
?>
