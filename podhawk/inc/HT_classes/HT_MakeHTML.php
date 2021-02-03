<?php

class HT_MakeHTML
{
	private $editor;
	private $reg; // instance of Registry

	public function __construct($editor)
	{
		$this->editor = $editor;

		$this->reg = Registry::instance();
	}

	public function make($rawText)
	{
		$editedText = $this->edit($rawText);

		$cleanText = $this->clean($editedText);

		return $cleanText;
	}

	private function edit($rawText)
	{
		if ($this->editor == '1')
		{
			$textile = new HT_Textile;
			$temphtml = $textile->TextileThis($rawText);
		}
		else
		{
			$temphtml = $rawText;
		}

		return $temphtml;
	}
		
	private function clean($editedText)
	{
		switch ($this->editor)
		{
			case 1: //Textile
			case 4: //TinyMCE

			$mod = new HT_Modifier($editedText);
			$additionalConfigArray = $mod->getAdditionalConfigArray();

			$p = new HT_Extended($additionalConfigArray);
			$cleanText = $p->purify($editedText);
			return $cleanText;
			break;

			// 2 = Markdown - no longer supported
			// 3 = BBCode - no longer supported

			case 5: //Very Simple Editor
			$p = new HT_Minimal;
			$cleanText = $p->purify($editedText);
			return $cleanText;
			break;

			case 6: //Raw HTML
			return $editedText;
			break;

			default:
			$p = new HT_Standard;
			$cleanText = $p->purify($editedText);
			return $cleanText;
			break;
		}
	}
}
?>
