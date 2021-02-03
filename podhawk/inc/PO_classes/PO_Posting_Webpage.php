<?php

class PO_Posting_Webpage extends PO_Posting_Extended
{
	protected $cacheName = 'posting';

	public function extendPostingData()
	{
		if (PH_CACHING == true)
		{
			$cache = new DA_Cache($this->cacheName . $this->id);

			$cachedPosting = $cache->getFromCache();

			if ($cachedPosting)
			{
				$this->posting = $cachedPosting;
				$this->updateDownloads();
			}		
			else
			{
				$this->getData();

				$cache->writeToCache($this->posting);
			}
		}
		else
		{
			$this->getData();
		}
	}

	protected function getData()
	{
		$this->posting['comment_on'] = $this->getCommentStatus();

		$this->posting['author'] = $this->getAuthorNickname();

		$this->posting['author_full_name'] = $this->getAuthorFullName();

		$this->posting['mediatypename'] = $this->getMediaTypeName();

		$this->posting['mime'] = $this->getMimeType($this->posting['audio_type']);

		$this->posting['web_link'] = $this->getIndirectAudioLink('web'); // download link

		$this->posting['audiourl'] = $this->getPlayerLink(); // player

		$this->posting['feedaudio'] = $this->getDirectAudioLink('pod'); // feed link

		$this->posting['playertype'] = $this->getPlayerType();

		$this->posting['addfiles'] = $this->getAddFiles();

		$this->posting['jw_vars'] = $this->getJWVars();

		$this->posting['permalink'] = $this->getPermalink($this->id);

		$this->posting['qtdata'] = $this->getQuicktime();

		$this->posting['show_downloads'] = $this->showDownloads();

		$this->posting['show_download_link'] = $this->showDownloadLink();

		$this->posting['links'] = $this->getAssociatedLinks();

		$this->posting['categories'] = $this->getAssociatedCategories();

		$this->posting['image'] = $this->getImageSrc();

		$t = new PO_Posting_Tags($this->posting['tags']);

		$this->posting['tag_array'] = $t->getTags();
	
		$this->posting['tag_links'] = $t->getTagLinks();

		if ($this->reg->findSetting('acceptcomments') == 'disqus') // to enable Disqus to find the posting title
		{
			$this->posting['title_uncoded'] = my_html_entity_decode($this->posting['title']);
		}

	}

	protected function getCommentStatus()
	{
		$status = ($this->reg->findSetting('acceptcomments') == 'none') ? '0' : $this->posting['comment_on'];

		return $status;
	}

	protected function getJWVars()
	{
		// outputs array of data needed to put jw player on screen

		$players = $this->reg->getPlayers();
		$return = array();

		if 	($this->posting['playertype'] == 'jwvideo' ||
			($this->posting['playertype'] == 'flash' && $players['audio_player_type'] == 'jwaudioplayer'))
		{
			$playlist_array = array(22,23,31,33); // these file types are playlists...
			$streamer_array = array(30,31,32,33); // .. and these are streaming videos

			// if we have a playlist, we define the playlistfile flashvar
			if (in_array($this->posting['audio_type'], $playlist_array))
			{
				$jw_vars['playlistfile'] 	=  $this->posting['audiourl'];

				// .. and some other flashvars needed for playlists
				$jw_vars['playlist.position'] 		= $players['jw_playlist'];
				$jw_vars['playlist.size'] 	= $players['jw_playlistsize'];
				$jw_vars['repeat'] 		= "list";

				// tell the player what sort of file it is
				$data = DataTables::AudioTypeData($this->posting['audio_type']);
				if (isset($data['provider']))
				{
					$jw_vars['provider'] = $data['provider'];
				}	
				 
			}
			else
			{

				// for streaming video, we need to define both the address of the server and the file to be streamed		
				if (in_array($this->posting['audio_type'], $streamer_array))
				{
					$jw_vars['file'] = $this->posting['jw_streaming_file'];
					$jw_vars['streamer'] = $this->posting['jw_streamer'];			
				}
	
				// in all other cases, we just define the file flashvar
				else
				{
					if(empty($this->posting['addfiles'])) //just a single file
					{
						$jw_vars['file'] = $this->posting['audiourl'];
					}
					else // there are several files
					{
						$levels[] = array('file' => $this->posting['audiourl'],
											'type' => $this->posting['mime']);
						foreach ($this->posting['addfiles'] as $addfile)
						{
							$levels[] = array ('file' => $addfile['audiourl'],
												'type' => $addfile['mime']);
						}

						$jw_vars['levels'] = $levels;
					}
				}
	
				// tell the player what sort of file it is
				$data = DataTables::AudioTypeData($this->posting['audio_type']);
				if (isset($data['provider']))
				{
					$jw_vars['provider'] = $data['provider'];
				}
			
				// define duration only if we know it - JW Player 5.2 does not like duration flashvar = 0
				if(!empty($this->posting['audio_length']))
				{
					$jw_vars['duration'] = $this->posting['audio_length'];
				}
			}

			// flashvars to control the colour of different parts of the player
			// These will be deprecated by LongTail video at some point
			// They work only when the player is in flash mode (not html5) and are
			// overriden if jw_use_skin_colours is 'true'
			if ($players['jw_use_skin_colours'] == false)
			{
				$jw_vars['backcolor'] 		= $players['jw_backcolor'];
				$jw_vars['frontcolor'] 	= $players['jw_frontcolor'];
				$jw_vars['lightcolor'] 	= $players['jw_lightcolor'];
				$jw_vars['screencolor'] 	= $players['jw_screencolor'];
			}

			// skin		
			if ($players['jw_skin'] != 'default')
			{
				$skinsDir = "podhawk/custom/players/jwplayer/skins/";
				$skin = $players['jw_skin'];

				if (substr($skin, -3) == 'swf')
				{
					$jw_vars['skin'] = $skinsDir . $skin;
				}
				elseif (file_exists($skinsDir . $skin . '/' . $skin . '.zip'))
				{
					$jw_vars['skin'] = $skinsDir . $skin . '/' . $skin . '.zip';
				}
			}
				
		
			// icons, controlbar position and stretching for the video player
			if ($this->posting['playertype'] == 'jwvideo')
			{
				$jw_vars['icons'] = ($players['jw_icons'] == '1') ? true : false;
				$jw_vars['controlbar.position'] = $players['jw_controlbar'];
				$jw_vars['stretching'] = $players['jw_stretching'];
			}

			// but not for the audio player
			else
			{
				$jw_vars['controlbar.position'] = 'bottom';
				$jw_vars['icons'] = false;
			}

			// have we an image to display on the screen
			if (!veryempty($this->posting['image']))
			{
				if (substr($this->posting['image'],0,7) == "http://")
				{
				$jw_vars['image'] = $this->posting['image'];
				}
				else
				{
					$jw_vars['image'] = THIS_URL . "/images/" . $this->posting['image'];
				}			
			}

			// define the height and width of the player
			// for mp3 and ogg audio files ...
			
			if ($this->posting['playertype'] == 'flash' || $this->posting['audio_type'] == 3)
			{
				$jw_vars['width'] = $players['jw_audio_width'];
				$jw_vars['height'] = $players['jw_audio_height'];
			
				// ..with an image..
				if (!veryempty($this->posting['image']))
				{
					$jw_vars['height'] = 250;
				}

			// ..and for the video player..			
			}
			else
			{			
					$jw_vars['width'] = $players['jw_video_width'];
					$jw_vars['height'] = $players['jw_video_height'];

				// ..and adjust to accomodate the playlist if there is one
				if (in_array($this->posting['audio_type'], $playlist_array))
				{
					// if the playlist is locally hosted, and is made up entirely of mp3 files, adjust the player height
					
					if ($this->posting['audio_type'] == 22 && $this->posting['filelocal'] == true)
					{
						$xml = new XM_Playlist(AUDIOPATH . $this->posting['audio_file']);
		
						if ($xml->allMP3()) 
						{
							$players['jw_video_height'] = $players['jw_audio_height'];
						}
					}

					if ($players['jw_playlist'] == 'bottom')
					{
						$jw_vars['height'] = $players['jw_video_height'] + $players['jw_playlistsize'];
					}

					if ($players['jw_playlist'] == 'right')
					{
						$jw_vars['width'] = $players['jw_video_width'] + $players['jw_playlistsize'];
					}
				}
		
			}
			
			$jw_vars['modes'][] = array ('type' => 'flash',
													'src' => 'podhawk/custom/players/jwplayer/player.swf');
			$jw_vars['modes'][] = array ('type' => 'html5'); 

			// get rid of the annoying escaping that php applies to characters such as '/'
			$return = str_replace("\\", '', json_encode($jw_vars));
		}

	return $return;
	}

	
	protected function showDownloads()
	{
		// do we want to display a "This file has been downloaded xxx times" link?

		if ($this->posting['countall'] == 0) $downloads = false; // to spare our blushes

		// if we are not counting downloads.....
		elseif ($this->reg->findSetting('countfla') == false
			&& $this->reg->findSetting('countweb') == false
			&& $this->reg->findSetting('countfla') == false)
		{
			$downloads = false;
		}

		// still to come - a setting to switch off display of downloads in the frontend
		//elseif ($this->reg->findSetting('show_downloads') == false) $downloads = false;

		// 'downloads' is meaningless for streaming files...
		elseif ($this->posting['audio_type'] > 29) $downloads = false;

		// .. and where there is no file to download
		elseif (!$this->posting['audio_file']) $downloads = false;

		else $downloads = true;		

		return $downloads;
	}

	protected function showDownloadLink()
	{
		// do we display a "Download ....." link for this media type
		$data = DataTables::AudioTypeData($this->posting['audio_type']);
		return ($data['download_link'] === true);
	}

	protected function getAddFiles()
	{
		$addfiles = array();

		if (!empty($this->posting['addfiles']))
		{
			$addfiles = unserialize ($this->posting['addfiles']);
	
			foreach ($addfiles as $key => $file)
			{
				// if the main file has an uncounted link, send the plain web address of the addfile
				if (strpos($this->posting['audiourl'], $this->posting['audio_file']) !== false)
				{
					$addFileAudioURL = THIS_URL . '/audio/' . $file['name'];
				}
				else // the addfile has the same web address as the main file, but with its own file extension
				{
					$strippedMainFile = substr($this->posting['audiourl'], 0, strrpos($this->posting['audiourl'], '.'));
					$addFileExtension = substr($file['name'], strrpos($file['name'], '.'));
					$addFileAudioURL = $strippedMainFile . $addFileExtension;
				}
				$addfiles[$key]['audiourl'] = $addFileAudioURL;
			}
		}
		return $addfiles;
	}
	
	private function updateDownloads()
	{
		$dosql = "SELECT countall FROM " . DB_PREFIX . "lb_postings WHERE id = {$this->id}";
		$result = $GLOBALS['lbdata']->getArray($dosql);

		$this->posting['countall'] = $result[0]['countall'];
	}
}
?>
