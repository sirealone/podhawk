<?php

class XM_FeedData
{
	protected $postings = array();
	protected $postingLinks = array();
	protected $postingComments = array();
	protected $iTunesCategories = array();
	protected $pagination; // instance of PO_Pagination_Feed
	protected $key; // id of current posting
	protected $commentKey; // key for comments array
	protected $withComments = false; // do we want comments in our feed?
	
	public function __construct($bool)
	{
		$this->reg = Registry::instance();

		$this->withComments = $bool;

		$lang = $this->reg->findSetting('language');
		require PATH_TO_ROOT . "/podhawk/lang/$lang.php";
		$this->trans = $trans_feed;

		$this->pagination = new PO_Pagination_Feed();
	
		$this->findPostings();

		$this->findITunesCategories();

	}

	public function setWithComments($bool)
	{
		$this->withComments = $bool;
	}

	public function setPostingKey($key)
	{
		$this->key = $key;
	}

	public function setCommentKey($key)
	{
		$this->commentKey = $key;
		$this->comment = $this->postingComments[$this->key][$key];
	}

	public function getChannelArray() // returns an array of data for the 'channel' component of the feed
	{
		$channelArray = array (	'title' 				=> $this->getTitle(),
								'link' 					=> $this->getLink(),
								'itunes:subtitle' 		=> $this->getSubtitle(),
								'description' 			=> $this->getDescription(),
								'language' 				=> $this->getLanguage(),
								'copyright' 			=> $this->getCopyright(),
								'itunes:owner' 			=> array('itunes:name' => $this->getAuthor(),
																'itunes:email' => $this->getEmail()),
								'managingEditor'		=> $this->getEditor(),
								'itunes:author' 		=> $this->getAuthor(),
								'image' 				=> array('url' 	=> $this->getRSSImageURL(),
																'title' => $this->getTitle(),
																'link' 	=> $this->getlink()),
								'pubDate' 				=> $this->getPubDate(),
								'lastBuildDate' 		=> $this->getLastUpdate(),
								'generator'				=> $this->getGenerator(),
								'itunes:explicit' 		=> $this->getExplicit()
								);
		return $channelArray;
	}

	public function getItemArray() // returns an array of data for each 'item' component
	{
		$itemArray = array (	'pubDate' 			=> $this->getPostingPubDate(),
								'title' 			=> $this->getPostingTitle(),
								'link' 				=> $this->getPostingLink(),
								'guid' 				=> $this->getPostingLink(),
								'comments'			=> $this->getPostingCommentsURL(),
								'itunes:author' 	=> $this->getPostingAuthor(),
								'itunes:explicit' 	=> $this->getPostingExplicit(),
								'itunes:subtitle' 	=> $this->getPostingITunesSubtitle(),
								'itunes:summary'	=> $this->getPostingITunesSummary(),
								'description' 		=> $this->getPostingDescription()
								);

		return $itemArray;
	}

	public function getEnclosureArray()
	{
		$attributeArray['url'] 		= $this->getFeedAudio();
		$attributeArray['length'] 	= $this->getAudioSize();
		$attributeArray['type'] 	= $this->getMimeType();

		$return['attributes'] = $attributeArray;

		$return['textNodes'] = array('itunes:duration' => $this->getITunesDuration());
	
		return $return;
	}

	public function getCommentArray()
	{
		$commentArray = array('guid' 			=> $this->getCommentURL(),
							'title' 			=> $this->getCommentTitle(),
							'link' 				=> $this->getCommentURL(),
							'itunes:author' 	=> $this->getCommentName(),
							'description' 		=> $this->getCommentDescription(),
							'itunes:summary' 	=> $this->getCommentITunesSummary(),
							'pubDate' 			=> $this->getCommentPubDate()
							);

		return $commentArray;
	}

	public function getCommentEnclosureArray()
	{
		$return['attributes'] = array (	'url' 		=> $this->getCommentDownloadLink(),
										'length' 	=> $this->getCommentAudioSize(),
										'type' 		=> $this->getCommentMimeType()
										);
		$return['textNodes'] = array('itunes:duration' => $this->getCommentITunesDuration());

		return $return;
	}	
	
	public function getPostings() // we need only the array keys
	{
		return array_keys($this->postings);
	}

	public function getPostingComments() // we need only array keys
	{

		foreach ($this->postingComments as $key => $comment)
		{
			$array[$key] = array_keys($comment);
		}
		return $array;
	}

	public function getCategories()
	{
		return $this->iTunesCategories;
	}

	public function getLastUpdate()
	{
		return $this->pagination->lastUpdate();
	}

	protected function getTitle()
	{
		$title = SITENAME;
		if (isset($_GET['cat']))
		{
			$title .= " : " . $_GET['cat'];
		}
		elseif (isset($_GET['id']) && isset($postings[$_GET['id']]['title']))
		{
			$title .= ' : ' . $postings[$_GET['id']]['title'];
		}
		return $title;
	}

	protected function getLink()
	{
		$link = THIS_URL;

		if (isset($_GET['id']))
		{
			$link .= "/index.php?id=" . $_GET['id'];
		}
		elseif(isset($_GET['cat']))
		{
			$link .= "/index.php?cat=" . rawurlencode($_GET['cat']);
		}
		return $link;
	}

	protected function getSubtitle()
	{
		return my_html_entity_decode($this->reg->findSetting('slogan'));
	}

	protected function getDescription()
	{
		return my_html_entity_decode($this->reg->findSetting('description'));
	}

	protected function getLanguage()
	{
		return $this->reg->findSetting('languagecode');
	}

	protected function getCopyright()
	{
		return strip_tags(my_html_entity_decode($this->reg->findSetting('copyright')));
	}

	protected function getAuthor()
	{
		return my_html_entity_decode($this->reg->findSetting('itunes_author'));
	}

	protected function getEmail()
	{
		return $this->reg->findSetting('itunes_email');
	}

	protected function getRSSImageURL()
	{
		return THIS_URL . '/images/rssimage.jpg';
	}

	protected function getEditor()
	{
		$ed = my_html_entity_decode($this->reg->findSetting('itunes_author'));

		$email = $this->reg->findSetting('itunes_email');

		$return = (empty($ed)) ? $email : $email . "($ed)";

		return $return;
	}

	protected function getPubDate()
	{
		return date('r');
	}

	protected function getGenerator()
	{
		return "PodHawk";
	}

	protected function getExplicit()
	{
		$return = ($this->reg->findSetting('itunes_explicit') == '1') ? 'yes' : 'no';
		return $return;
	}

	public function getITunesImageURL()
	{
		return THIS_URL . '/images/itunescover.jpg';
	}

	protected function getPostingPubDate()
	{
		return  date("r", strtotime($this->postings[$this->key]['posted']));
	}

	protected function getPostingTitle()
	{
		return my_html_entity_decode($this->postings[$this->key]['title']);
	}

	protected function getPostingLink()
	{
		return $this->postings[$this->key]['permalink'];
	}

	protected function getPostingCommentsURL()
	{
		if ($this->reg->findSetting('acceptcomments') != 'none' && $this->postings[$this->key]['comment_on'] == true)
		{
			return $this->postings[$this->key]['permalink'] . "?id={$this->key}#comments";
		}
		else
		{
			return false;
		}
	}

	protected function getPostingAuthor()
	{
		return my_html_entity_decode($this->postings[$this->key]['author_full_name']);
	}

	protected function getPostingExplicit()
	{
		$return = ($this->reg->findSetting('itunes_explicit') == '1' || $this->postings[$this->key]['itunes_explicit'] == '1') ? 'yes' : 'no';
		return $return;
	}

	protected function getPostingITunesSubtitle()
	{
		if (!empty($this->postings[$this->key]['summary']))
		{
			$subtitle = $this->postings[$this->key]['summary'];
		}
		else
		{
			$short = new HT_Shortener($this->postings[$this->key]['message_html'], 255);

			$subtitle = $short->getShortText();
		}

		return trim(my_html_entity_decode(strip_tags($subtitle)));
	}

	protected function getPostingITunesSummary()
	{
		$short = new HT_Shortener($this->postings[$this->key]['message_html'], 4000);

		$summary = $short->getShortText();
		
		return trim(my_html_entity_decode(strip_tags($summary)));		
	}

	public function hasAudioFile() // is there a media attachment, and does it have a mime type? (attachments without mime types include eg YouTube videos.)
	{
		return (!empty($this->postings[$this->key]['audio_file']) && !empty($this->postings[$this->key]['mimetype']));
	}

	protected function getFeedAudio()
	{
		return $this->postings[$this->key]['feedaudio'];
	}

	protected function getAudioSize()
	{
		return $this->postings[$this->key]['audio_size'];
	}

	protected function getMimeType()
	{
		return $this->postings[$this->key]['mimetype'];
	}

	protected function getITunesDuration()
	{
		return $this->postings[$this->key]['itunesduration'];
	}

	protected function getPostingDescription()
	{		
		$desc = my_html_entity_decode($this->postings[$this->key]['message_html']);

		if (!empty($this->postingLinks[$this->key]))
		{
			$desc .= '<ul>';
			foreach ($this->postingLinks[$this->key] as $link)
			{
				$desc .= "<li><a href=\"{$link['url']}\" title=\"{$link['description']}\">{$link['title']}</a> :: {$link['description']}</li>";
			}
			$desc .= '</ul>';
		}

		if (!empty($posting['feedaudio']))
		{
			$minutes = $this->getMinutes ($this->postings[$this->key]['audio_length']);
			$megabytes = $this->getMegaBytes($this->postings[$this->key]['audio_size']);
			$desc .= "<p><a href=\"{$posting['feedaudio']}\">{$this->trans['download']} ($minutes mins | $megabytes MB)</a></p>";
		}
		return trim($desc);		
	}

	protected function getCommentURL()
	{
		return THIS_URL . "/index.php?id={$this->key}#com{$this->comment['id']}";
	}

	protected function getCommentTitle()
	{
		$commentName = $this->getCommentName();
		return my_html_entity_decode($this->postings[$this->key]['title']) . " : {$this->trans['comment_by']} $commentName";
	}

	protected function getCommentName()
	{
		return my_html_entity_decode($this->comment['name']);
	}

	protected function getCommentDescription()
	{
		$return = my_html_entity_decode($this->comment['message_html']);

		if (!empty($this->comment['audio_file']))
		{
			$minutes = $this->getMinutes($this->comment['audio_length']);
			$megabytes = $this->getMegaBytes($this->comment['audio_size']);

			$return .= "<p><a href=\"" . THIS_URL . "/{$this->comment['downloadlink']}\">{$this->trans['download']} ($minutes mins / $megabytes MB)</a></p>";
		}

		return trim($return);
	}

	protected function getCommentITunesSummary()
	{
		return trim(my_html_entity_decode(strip_tags($this->comment['message_html'])));
	}

	protected function getCommentPubDate()
	{
		return date("r", strtotime($this->comment['posted']));
	}

	public function commentHasAudio()
	{
		$return = (!empty($this->comment['audio_file']));
		return $return;
	}

	public function getCommentDownloadLink()
	{
		return THIS_URL . '/' . $this->comment['downloadlink'];
	}

	protected function getCommentAudioSize()
	{
		return $this->comment['audio_size'];
	}

	protected function getCommentMimeType()
	{
		return $this->comment['mimetype'];
	}

	protected function getCommentITunesDuration()
	{
		return $this->comment['itunesduration'];
	}

	public function getAtomFeedAddress()
	{
		// why does PHP require all this just to return the full URL ???
		$protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		$return = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	
		// add query string,if there is one
		if (isset($_GET))
		{
			$queryString = '?' . (http_build_query($_GET));
			$return .= $queryString;			
		}
		return $return;
		
	}

	protected function findPostings()
	{
		$rawPostings = $this->pagination->getRows();

		if (count($rawPostings) > 0)
		{
			foreach ($rawPostings as $posting)
			{
				$postingManager = new PO_Posting_Feed($posting);

				$postingManager->extendPostingData();

				$key = $postingManager->getID();

				$this->postings[$key] = $postingManager->getPosting();

				$this->postingLinks[$key] = $postingManager->getAssociatedLinks();

				if ($this->withComments == true)
				{
					$comments = new PO_Posting_Comments($key);
					$this->postingComments[$key] = $comments->getComments();
				}
			}
		}
	}
	
	protected function findITunesCategories()
	{
		$cat_table = DataTables::itunescats();
		
		$allcats = array(	$this->reg->findSetting('feedcat1'),
							$this->reg->findSetting('feedcat2'),
							$this->reg->findSetting('feedcat3'),
							$this->reg->findSetting('feedcat4'));

		foreach ($allcats as $thiscat)
		{   
			if ($thiscat != "00-00")
			{         
				$maincat = substr($thiscat,0,2) . "-00";
				$maincat_name = $cat_table[$maincat];	 
				$subsidcat_name = $cat_table[$thiscat];
				$this->iTunesCategories[] = array('main'=>$maincat_name,'subsid'=>$subsidcat_name);
			 }
		}
	}

	protected function getMinutes($sec)
	{
		$min = (int) ($sec / 60);
    	$min2 = $sec%60;
    	if ($min2 < 10) { $min2 = "0" . $min2; }
    	return $min.":".$min2;
	}

	protected function getMegaBytes($request)
	{
		$mb = $request / 1024 / 1024;
		$mb = round ($mb, 1);
		if ($mb == 0) { $mb = 0.1; }
		if ($request < 10) { $mb = 0; };
		return $mb;
	}	
}
?>
