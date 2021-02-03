<?php

class PO_Posting_LastPostings extends PO_Posting_Extended
{

	public function extendPostingData()
	{
		$this->posting['permalink'] = $this->getPermalink($this->id);

		$this->posting['author'] = $this->getAuthorNickname();

		$t = new PO_Posting_Tags($this->posting['tags']);

		$this->posting['tag_array'] = $t->getTags();
	
		$this->posting['tag_links'] = $t->getTagLinks();

		$this->posting['image'] = $this->getImageSrc();

	}
}
?>
