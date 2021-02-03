<?php

class PO_Posting_Feed extends PO_Posting_Extended
{
	public function extendPostingData()
	{

		$this->posting['permalink']			= $this->getPermalink($this->id);
		$this->posting['author_full_name'] 	= $this->getAuthorFullName();
		$this->posting['feedaudio'] 		= $this->getDirectAudioLink('pod');
		$this->posting['itunesduration'] 	= $this->getiTunesDuration($this->posting['audio_length']);
		$this->posting['mimetype'] 			= $this->getMimeType($this->posting['audio_type']);

	}

}
?>
