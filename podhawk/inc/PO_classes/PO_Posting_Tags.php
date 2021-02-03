<?php

class PO_Posting_Tags
{

	private $tagnames = array();
	private $taglinks = array();
	private $tag_string;

	public function __construct($tags)
	{
		$this->tag_string = $tags;
	}

	public function getTags()
	{
		if (empty($this->tagnames))
		{
			$this->findTagNames();
		}

		return $this->tagnames;
	}

	public function getTagLinks()
	{
		if (empty($this->taglinks))
		{
			$this->findTagLinks();
		}

		return $this->taglinks;
	}
	
	private function findTagNames()
	{
		$tags = explode(' ', $this->tag_string);

		$array = array();

		foreach ($tags as $tag)
		{
			if (empty($tag) || isset($array[$tag])) continue;
			
			$array[$tag] = 1;
		}

		$keys = array_keys ($array);

		natcasesort($keys);

		$this->tagnames = $keys;
	}

	private function findTagLinks()
	{
		if (empty($this->tagnames))
		{
			$this->findTagNames();
		}

		foreach ($this->tagnames as $tag)
		{
			$tag_encoded = rawurlencode(my_html_entity_decode($tag));
			$this->taglinks[$tag] = 'index.php?tag=' . $tag_encoded;
		}
	}
}
?>
