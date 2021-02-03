<?php

class PO_Permalink
{

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function permalink()
	{
		return PO_Posting_Extended::getPermalink($this->id);
	}
}
?>
