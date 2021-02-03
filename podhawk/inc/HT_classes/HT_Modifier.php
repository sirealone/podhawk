<?php

class HT_Modifier
{
// class used to detect if the post message contains elements which we need to 
// allow in HTML Purifier

	private $message; // the message text we want to check
	private $additionalConfigArray = array();

	public function __construct($message)
	{
		$this->message = $message;

		$this->scanMessage();
	}
	
	public function getAdditionalConfigArray()
	// any further "rel='....' elements which we need to allow?
	{
		return $this->additionalConfigArray;
	}

	private function scanMessage()
	{
		// lightbox
		$regex = "/rel\s?=\s?[\'|\"](lightbox(\[[A-Za-z0-9-_]*\])?)/";

		preg_match_all ($regex, $this->message, $matches);

		$this->additionalConfigArray['rel'] = array_values(array_unique($matches[1]));

	}
}
?>	
