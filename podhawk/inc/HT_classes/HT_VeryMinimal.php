<?php

class HT_VeryMinimal extends HT_Purifier
{
	protected function setConfig()
	{
		$this->config->set('HTML.AllowedElements', 'p'); // allow only <p> elements

		$this->config->set('AutoFormat.AutoParagraph', true); // double line breaks become <p>..</p>
	}
}
?>
