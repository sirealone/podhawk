<?php

class HT_MakeCommentHTML
{
	private $reg; //instance of Registry

	public function __construct()
	{
		$this->reg = Registry::instance();
	}

	public function make($rawText)
	{
		if ($this->reg->findSetting('comment_text_editor') == TRUE)
		{
			$p = new HT_Comment;			
		}
		else
		{
			$p = new HT_VeryMinimal;
		}
		
		$cleanText = $p->purify($rawText);

		return $cleanText;
	}
}
?>
