<?php

abstract class HT_Purifier

{
	protected $config; // configuration object

	public function __construct()
	{
		require_once PATH_TO_ROOT . '/podhawk/html_purifier/HTMLPurifier.auto.php';

		$this->config = HTMLPurifier_Config::createDefault();

		$this->setConfig();

		$this->config->set('HTML.Doctype', 'XHTML 1.0 Transitional');

		$purifier_cache = substr(HTML_PURIFIER_CACHE, 0, -1); //remove trailing slash

		$this->config->set('Cache.SerializerPath', $purifier_cache);

		$this->purifier = new HTMLPurifier($this->config);
	}

	protected abstract function setConfig();

	public function purify($text)
	{
		return $this->purifier->purify($text);
	}
}
?>
