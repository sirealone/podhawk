<?php

class HT_Standard extends HT_Purifier
{

	protected function setConfig()
	{
		// we use default config settings plus the following

		$this->config->set('URI.Base', THIS_URL .'/'); // base for making relative URLs absolute

		$this->config->set('URI.MakeAbsolute', TRUE); // make relative URLs absolute


	}
}
?>
