<?php

class HT_Extended extends HT_Purifier
{
	private $additionalConfigParams = array();

	public function __construct($additionalConfigParams)
	{
		$this->additionalConfigParams = $additionalConfigParams;
		parent::__construct();
	}

	protected function setConfig()
	{
		$relArray = array('nofollow', 'print');

		if (isset($this->additionalConfigParams['rel']))
		{
			$relArray = array_merge($relArray, $this->additionalConfigParams['rel']);
		}

		$this->config->set('HTML.Trusted', TRUE); // enables iframe and script tags

		$this->config->set('Attr.AllowedFrameTargets', array ('_blank', '_self', '_parent', '_top', 'blank', 'self', 'parent', 'top')); // target="blank"
	
		$this->config->set('HTML.SafeObject', TRUE); // enables object tags

		$this->config->set('HTML.SafeEmbed', TRUE);

		$this->config->set('Output.FlashCompat', TRUE);

		$this->config->set('HTML.FlashAllowFullScreen', TRUE);

		$this->config->set('Attr.AllowedRel', $relArray); // enables rel="..." attributes

		$this->config->set('Attr.EnableID', true); // enable ID attributes

		$this->config->set('URI.Base', THIS_URL .'/'); // base for making relative URLs absolute

		$this->config->set('URI.MakeAbsolute', TRUE); // make relative URLs absolute
	}
}
?>
