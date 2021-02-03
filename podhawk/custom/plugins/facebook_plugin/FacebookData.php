<?php

class FacebookData extends PO_Posting_Webpage
{
	protected $cacheName;

	public function __construct($posting)
	{
		parent::__construct($posting);

		$this->cacheName = ACTION;

	}

	public function getPosting()
	{
		 // we call shortenPosting() here and not as part of the getData() function, so that the full posting text, not the shortened text, is cached
		$this->shortenPosting();

		return $this->posting;
	}


	protected function getData()
	{
		$this->posting['author'] = $this->getAuthorNickname();

		$this->posting['mediatypename'] = $this->getMediaTypeName();

		$this->posting['mime'] = $this->getMimeType($this->posting['audio_type']);

		$this->posting['playertype'] = $this->getPlayerType();

		$this->posting['web_link'] = $this->getIndirectAudioLink('web');

		$this->posting['audiourl'] = $this->getPlayerLink();

		$this->posting['image'] = $this->getImageSrc();

		$this->posting['imgsrc'] = $this->getYouTubeImage();

		$this->posting['post_url'] = $this->makePostingURL();

		$this->posting['permalink'] = $this->getPermalink($this->id);

		$this->posting['message_html'] = $this->removeImages();

		$this->posting['jw_vars'] = $this->getJWVars();
		
	}

	
	protected function getYouTubeImage()
	{
		if ($this->posting['audio_type'] == 21)
		{
			$img = str_replace("/v/", "/vi/", $this->posting['audio_file'])."/2.jpg";
			$img = str_replace("www.", "img.", $img);

			return $img;
		}
		else
		{
			return false;
		}
	}

	protected function removeImages()
	{
		// remove any divs made by the image manager
		$regex = "/<div class=\"lb_image_.*<\/div>/";
		$return = preg_replace($regex, '', $this->posting['message_html']);

		// remove any other images
		$regex = "/<img.*\/>/";
		$return = preg_replace($regex, '', $return);

		return $return;

	}

	protected function makePostingURL()
	{
		$baseURL = (ACTION == 'facebook_apptab') ? 'apptab.php' : THIS_URL . '/podhawk/custom/plugins/facebook_plugin/index.php';

		$queryString = "?id={$this->id}";
		
		return $baseURL . $queryString;
	}

	protected function shortenPosting()
	{
		$this->posting['shortened'] = false;

		$plugins = Plugins::instance();

		$divider = $plugins->getParam('short_text', 'divider');

		if ($plugins->enabled('short_text') && strpos($this->posting['message_html'], $divider) !== false)
		{
			if (!isset($_GET['id']))
			{
				$st = $plugins->plugins->short_text;
				$this->posting['message_html'] = $st->makeShortText($this->posting['message_html'], $this->id, $this->posting['post_url']);
				$this->posting['shortened'] = true;
			}
			else
			{
				$this->posting['message_html'] = str_replace($divider, '', $this->posting['message_html']);
			}
		}
		
	}

	protected function getJWVars()
	{
		$jw_vars = parent::getJWVars();

		//override some of the flashvars

		// use the simple skin if available, otherwise the default skin
		if (file_exists('../../players/jwplayer/skins/simple.swf'))
		{
			$jw_vars['flashvars']['skin'] = '../../players/jwplayer/skin/simple.swf';
		}
		else
		{
			unset($jw_vars['flashvars']['skin']);
		}

		// make size of player more appropriate for a Facebook page		
		if ($this->posting['playertype'] == 'jwvideo')
		{
			// leave 'all-mp3' playlists untouched
			if ($this->posting['audio_type'] != 22 || $this->posting['filelocal'] == true)
			{
				// standard dimensions for other playlists
				if (isset($jw_vars['flashvars']['playlist.position']))
				{
					$jw_vars['flashvars']['playlist.position'] = 'bottom';
					$jw_vars['flashvars']['playlist.size'] 	= 120;
					$jw_vars['height'] = 375;
				}
				// standard video player dimensions (but not for ogg audio)
				elseif ($this->posting['audio_type'] != 3)
				{
					$jw_vars['height'] = 255;
					$jw_vars['width'] = 340;
				}
			}
		}
		
		return $jw_vars;
				
	}
}

?>
