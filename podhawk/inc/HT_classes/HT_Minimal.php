<?php

class HT_Minimal extends HT_Purifier
{
	protected function setConfig()
	{
		$this->config->set('HTML.AllowedElements', 'p, a'); // allow only the <p> and <a> elements

		$this->config->set('HTML.AllowedAttributes', 'a.href'); // allow <a> to have 'href' attribute

		$this->config->set('AutoFormat.Linkify', TRUE); // create links from 'http://....'

		$this->config->set('URI.Base', THIS_URL .'/'); // base for making relative URLs absolute

		$this->config->set('URI.MakeAbsolute', TRUE); // make relative URLs absolute

		$this->config->set('AutoFormat.AutoParagraph', true); // double line breaks become <p>..</p>
	}
}
?>
