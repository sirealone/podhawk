<?php

class XM_RSSFeed
{

	private $postings = array(); // array of posting data
	private $posting_comments = array(); // array of comments about postings
	private $dom; // instance of DOMDocument
	private $reg; // instance of Registry
	private $rss; // instance of DOMElement representing the 'rss' element
	private $channel; // ditto the 'channel' element
	private $item; // ditto the current posting 'item' element
	private $comment; // ditto the current comment 'item' element
	private $withComments = false; // do we want comments in the feed
	private $lastUpdate;
	private $iTunesCategories = array();

	public function __construct($dataSource, $bool = false)
	{
		$this->data = new $dataSource($bool);
		$this->postings = $this->data->getPostings(); // we need only an array of the posting ids (the array keys)

		if ($bool == true)
		{
			$this->posting_comments = $this->data->getPostingComments();
			$this->withComments = true;
		}

		$this->dom = new DOMDocument('1.0', 'UTF-8');
		$this->dom->formatOutput = true;
	}

	public function build()
	{
		$this->createRoot(); // rss root element

		$this->channel = $this->dom->createElement('channel'); // the channel element

		$this->rss->appendChild($this->channel);

		$this->createAtom(); // atom

		$channelArray = $this->data->getChannelArray(); // get array of data for the channel element

		foreach ($channelArray as $name => $text)
		{
			if ($text)
			{
				$this->append('channel', $name, $text);
			}
		}

		// iTunes image...
		$iTunesImage = $this->dom->createElement('itunes:image');
		$iTunesImage->setAttribute('href', $this->data->getITunesImageURL());
		$this->channel->appendChild($iTunesImage);
		

		// .... and the iTunes Categories
		$this->iTunesCategoryElements();

		// then item elements for each posting
		foreach ($this->postings as $key)
		{

			$this->data->setPostingKey($key);

			$this->item = $this->dom->createElement('item');
		
			$itemArray = $this->data->getItemArray();

			foreach ($itemArray as $name => $text)
			{
				if ($text)
				{
					$this->append ('item', $name, $text);
				}
			}

			// iTunes image...
			$iTunesItemImage = $this->dom->createElement('itunes:image');
			$iTunesItemImage->setAttribute('href', $this->data->getITunesImageURL());
			$this->item->appendChild($iTunesItemImage);			

			$hasAudio = $this->data->hasAudioFile();
			if ($hasAudio == true)
			{
				$enclosure = $this->dom->createElement('enclosure');
				$enclosureArray = $this->data->getEnclosureArray();

				foreach ($enclosureArray['attributes'] as $name => $value)
				{
					$enclosure->setAttribute($name, $value);
				}
				
				$this->item->appendChild($enclosure);

				foreach ($enclosureArray['textNodes'] as $name => $value)
				{
					$this->append('item', $name, $value);
				}
			}

			$this->channel->appendChild($this->item);

			// .... including elements for comments if requested
			if ($this->withComments == true)
			{
				foreach ($this->posting_comments[$key] as $commentIndex)
				{
					$this->data->setCommentKey($commentIndex);

					$this->comment = $this->dom->createElement('item');

					$commentArray = $this->data->getCommentArray();

					foreach ($commentArray as $name => $text)
					{
						$this->append('comment', $name, $text);
					}

					$commentHasAudio = $this->data->commentHasAudio();
					if ($commentHasAudio == true)
					{
						$enclosure = $this->dom->createElement('enclosure');

						$commentEnclosureArray = $this->data->getCommentEnclosureArray();

						foreach ($commentEnclosureArray['attributes'] as $name => $value)
						{
							$enclosure->setAttribute($name, $value);
						}

						$this->comment->appendChild($enclosure);
		
						foreach ($commentEnclosureArray['textNodes'] as $name => $value)
						{
							$this->append('comment', $name, $value);
						}
						
					}

					$this->channel->appendChild($this->comment);

				} // close comments loop
			}// close 'if comments'
		} // close postings loop	
				
		$xml = $this->dom->saveXML();

		return $xml;
	}

	private function createRoot()
	{
		$this->rss = $this->dom->createElement('rss');
		
		$namespaceURL = array(
				'itunes' 	=> 'http://www.itunes.com/dtds/podcast-1.0.dtd',
				'atom' 		=> 'http://www.w3.org/2005/Atom'
				);

		foreach ($namespaceURL as $name => $url)
		{
			$this->rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $name, $url);
		}

		$this->rss->setAttribute('version', '2.0');
		$this->dom->appendChild($this->rss);
	}
	
	private function createAtom()
	{
		$atom = $this->dom->createElement('atom:link');
		$atom->setAttribute('href', $this->data->getAtomFeedAddress());
		$atom->setAttribute('rel', 'self');
		$atom->setAttribute('type', 'application/rss+xml');

		$this->channel->appendChild($atom);
	}

	private function append($parentElement, $name, $text)
	{
		try
		{
			if (is_string($parentElement))
			{
				if (property_exists ($this, $parentElement))
				{
					$parent = $this->$parentElement;
				}
				else
				{
					throw new Exception ("Parent Element $parentElement is not known");
				}
			}
			else
			{
				if ($parentElement instanceof DOMElement)
				{
					$parent = $parentElement;
				}
				else
				{
					throw new Exception ("Parent element must be an instance of the DOMElement Class");
				}
			}

			$name = $this->dom->createElement($name);
			$parent->appendChild($name);

			if (is_array($text))
			{
				foreach ($text as $name1=>$text1)
				{
					$this->append($name, $name1, $text1);
				}
			}

			else
			{
				if (!empty($text))
				{			
					$textNode = $this->dom->createTextNode($text);
					$name->appendChild($textNode);
				}			
			}
		}
		catch (Exception $e)
		{
			header("Content-Type: text/plain");
			die ("Error in creating RSS feed - " . $e->getMessage());
		}
	}

	private function iTunesCategoryElements()
	{
		$iTunesCategories = $this->data->getCategories();

		foreach ($iTunesCategories as $category)
		{
			$iTunesCatElement = $this->dom->createElement('itunes:category');
			$iTunesCatElement->setAttribute('text', my_html_entity_decode($category['main']));

			if ($category['subsid'] != $category['main'])
			{
				$subsidElement = $this->dom->createElement('itunes:category');
				$subsidElement->setAttribute('text', my_html_entity_decode($category['subsid']));
				$iTunesCatElement->appendChild($subsidElement);
			}

			$this->channel->appendChild($iTunesCatElement);
		}
		foreach ($iTunesCategories as $category)
		{
			$catElement = $this->dom->createElement('category');
			$catElement->appendChild($this->dom->createTextNode(my_html_entity_decode($category['subsid'])));
			$this->channel->appendChild($catElement);
		}
	}	
}
?>
