<?php

class mediaelement extends PluginPattern
{
	private $MERequired;
	private $retainJWPlayer;
	private $spacing = array(); // array of style elements for spacing above and below the player
	

	public function __construct($data=NULL)
	{
		$this->myName = 'mediaelement';
		$this->myFullName = 'MediaElement Video and Audio Player';

		$this->langFileLocation = $this->getLangFileLocation();
		$this->trans = $this->getTranslationArray($this->myName);

		$this->description = $this->trans['description'];
		$this->version = "1.0";
		$this->author = 'Peter Carter';
		$this->contact = 'cpetercarter@googlemail.com';		

		$this->initial_params = array(	'videoWidth' 		=> '400',
										'videoHeight'		=> '225',
										'videoMargin'		=> '20',
										'audioWidth' 		=> '300',
										'audioHeight' 		=> '30',
										'audioMargin'		=> '40',
										'audioMarginBelow' 	=> '20',
										'skin' 				=> 'default',
										'bgColour'			=> '',
										'preload'			=> 'none');

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initial_params;
		$this->enabled = (!empty($data['enabled'])) ? $data['enabled'] : '0';

		$this->listeners = array('addCSS', 'addHeadScript', 'onPostingDataReady', 'onBackendPostingDataReady', 'onAllPageDataReady');
	}

	protected function backendPluginsPage()
	{
		$background =  (strpos($this->params['bgColour'], '#') !== false) ? strtoupper($this->params['bgColour']): $this->params['bgColour'];

		$html = <<<EOF
<tr>
	<td colspan="3" class="right">{$this->trans['dimensions_help']}</td>
</tr>
<tr>
	<td class="left">{$this->trans['video_width']} :</td>
	<td class="center"><input class="narrow" type="text" name="videoWidth" value="{$this->params['videoWidth']}" /> pixels</td>
	<td class="right"></td>
</tr>
<tr>
	<td class="left">{$this->trans['video_height']} :</td>
	<td class="center"><input class="narrow" type="text" name="videoHeight" value="{$this->params['videoHeight']}" /> pixels</td>
	<td></td>
</tr>
<tr>
	<td class="left">{$this->trans['video_top_margin']} :</td>
	<td class="center"><input class="narrow" type="text" name="videoMargin" value="{$this->params['videoMargin']}" /> pixels</td>
	<td class="right">{$this->trans['margin_help']}</td>
</tr>
<tr>
	<td class="left">{$this->trans['audio_width']} :</td>
	<td class="center"><input class="narrow" type="text" name="audioWidth" value="{$this->params['audioWidth']}" /> pixels</td>
	<td class="right"></td>
</tr>
<tr>
	<td class="left">{$this->trans['audio_height']} :</td>
	<td class="center"><input class="narrow" type="text" name="audioHeight" value="{$this->params['audioHeight']}" /> pixels</td>
	<td class="right">30 px is a good height for default and One Designs skins; 60 px for TED and WMP skins.</td>
</tr>
<tr>
	<td class="left">{$this->trans['audio_top_margin']} :</td>
	<td class="center"><input class="narrow" type="text" name="audioMargin" value="{$this->params['audioMargin']}" /> pixels</td>
	<td class="right">{$this->trans['margin_help']}</td>
</tr>
<tr>
	<td class="left">{$this->trans['margin_below']} :</td>
	<td class="center"><input class="narrow" type="text" name="audioMarginBelow" value="{$this->params['audioMarginBelow']}" /> pixels</td>
	<td class="right">{$this->trans['margin_help']}</td>
</tr>
<tr>
	<td class="left">{$this->trans['skin']} :</td>
	<td class="center">
		<select class="narrow" name="skin">
EOF;
		$skins = array('default', 'TED', 'WMP', 'One Designs');
		foreach ($skins as $skin)
		{
			if ($this->params['skin'] == strtolower($skin))
			{
				$html .= "<option value=\"$skin\" selected=\"selected\">$skin skin</option>";
			}
			else
			{
				$html .= "<option value=\"$skin\">$skin skin</option>";
			}
		}
		$html .= <<<EOF
	</select></td>
	<td></td>
</tr>
<tr>
	<td class="left">{$this->trans['bgcolour']} :</td>
	<td class="center">
		<input class="narrow color {pickerPosition:'right', required: false, hash: true, adjust: false}" type="text" name="bgColour" value="$background" />
	</td>
	<td class="right">{$this->trans['bgcolour_help']}</td>
</tr>
<tr>
	<td class="left">{$this->trans['preload']}</td>
	<td class="center">
	<select class="narrow" name="preload">
EOF;
	$preloadOptions = array ('none', 'metadata', 'auto', 'browser');
	foreach ($preloadOptions as $option)
	{
		if ($this->params['preload'] == $option)
		{
			$html .= "<option value=\"$option\" selected=\"selected\">{$this->trans[$option]}</option>";
		}
		else
		{
			$html .= "<option value=\"$option\">{$this->trans[$option]}</option>";
		}
	}
	$html .= <<<EOF
	</select>
	</td>
	<td class="right">{$this->trans['preload_help']}</td>
</tr>
<tr>
<td class="right" colspan="3">
{$this->trans['preload_warning']}</td>
</tr>
EOF;

	return $html;

	}

	protected function getParamsFromPosts()
	{
		if ($this->checkColour($_POST['bgColour']) != 1)
		{
			$_POST['bgColour'] = '';
		}

		$p = array('videoWidth', 'videoHeight', 'videoMargin', 'audioWidth', 'audioHeight', 'audioMargin', 'audioMarginBelow', 'skin', 'bgColour', 'preload');
		foreach ($p as $pp)
		{
			$params[$pp] = strtolower($_POST[$pp]);
		}
		return $params;
	}

	public function onPostingDataReady($postings)
	{
		$this->MERequired = false;
		$this->retainJWPlayer = false;
		$return = array();

		foreach ($postings as $key => $posting)
		{
			$data = DataTables::AudioTypeData($posting['audio_type']);

			if (isset($data['html5']) && $data['html5'] == true)
			{
				$this->MERequired = true;

				$class = ($this->params['skin'] == 'ted' || $this->params['skin'] == 'wmp') ? "class=\"mejs-{$this->params['skin']}\"" : '';

				$preload = ($this->params['preload'] == 'browser') ? '' : "preload=\"{$this->params['preload']}\"";

				if ($posting['audio_type'] == 1 || $posting['audio_type'] == 2 || $posting['audio_type'] == 3) // audio files
				{
					$html = "<audio $class id=\"me_player$key\" width=\"{$this->params['audioWidth']}\" height=\"{$this->params['audioHeight']}\" controls=\"controls\" $preload>\n";
					$html .= "<source type=\"{$posting['mime']}\" src=\"{$posting['audiourl']}\" />\n";
					if (!empty($posting['addfiles']))
					{
						foreach ($posting['addfiles'] as $addfile)
						{
							$html .= "<source type=\"{$addfile['mime']}\" src=\"{$addfile['audiourl']}\" />\n";
						}
					}
					$html .= "</audio>\n";

					$this->spacing[] = "#podhawk_player_$key {margin-top: {$this->params['audioMargin']}px; margin-bottom: {$this->params['audioMarginBelow']}px;}";
					
				}
				else // video files
				{
					$poster = (empty($posting['image'])) ? '' : "poster=\"{$posting['image']}";

					$html = "<video $class id=\"me_player$key\" width=\"{$this->params['videoWidth']}\" height=\"{$this->params['videoHeight']}\" controls=\"controls\" $preload $poster>\n";
					$html .= "<source type=\"{$posting['mime']}\" src=\"{$posting['audiourl']}\" />\n";
					if (!empty($posting['addfiles']))
					{
						foreach ($posting['addfiles'] as $addfile)
						{
							$html .= "<source type=\"{$addfile['mime']}\" src=\"{$addfile['audiourl']}\" />\n";
						}
					}
					$html .= "</video>\n";
				
					$this->spacing[] = "#podhawk_player_$key {margin-top: {$this->params['videoMargin']}px; margin-bottom: {$this->params['audioMarginBelow']}px;}";
				}
				
				$return[] = array('plugin' => $this->myName,
									'variable' => 'postings',
									'offset' => array($key, 'plugin_player'),
									'value' => $html);
			}
			// don't remove JW Player stuff if it is needed for a media file (eg a You Tube video) which cannot be played in HTML5
			elseif ($data['player'] == 'jwvideo'  && $data['html5'] == false)
			{
				$this->retainJWPlayer = true;
			}			
		}
	return $return;
	}

	public function addCSS()
	{
		$return = array();
		if ($this->MERequired)
		{
			if (ACTION == 'webpage')
			{
				if ($this->params['skin'] == 'one designs')
				{
					$return[] = "<link rel=\"stylesheet\" href=\"podhawk/custom/plugins/{$this->myName}/onedesign/mediaelementplayer.css\" />";
				}
				else
				{
					$return[] = "<link rel=\"stylesheet\" href=\"podhawk/custom/plugins/{$this->myName}/mediaelementplayer.css\" />";

					if ($this->params['skin'] != 'default')
					{
						$return[] = "<link rel=\"stylesheet\" href=\"podhawk/custom/plugins/{$this->myName}/mejs-skins.css\" />";
					}
				}
				// background colour
				if (!empty($this->params['bgColour']) && $this->params['bgColour'] != 'default')
				{
					$bgColour = ($this->params['bgColour'] == 'random') ? $this->randomColour() : $this->params['bgColour'];

					$return[] = "<style> div.mejs-container div.mejs-controls { background: $bgColour;}</style>";
				}
				if (!empty($this->spacing))
				{
					$return[] = "<style>\n" . implode ( "\n", $this->spacing) . "\n</style>";
				}
					
			}
			elseif (ACTION == 'backend') // backend record2 page
			{
				$return[] = "<link rel=\"stylesheet\" href=\"custom/plugins/{$this->myName}/mediaelementplayer.css\" />";
			}
		}
		
		return $return;
	}

	public function addHeadScript()
	{
		$return = array();
		if ($this->MERequired)
		{
			if (ACTION == 'webpage')
			{
				$return[] = "<script type=\"text/javascript\" src=\"podhawk/custom/plugins/{$this->myName}/mediaelement-and-player.min.js\"></script>";
			}
			elseif (ACTION == 'backend')
			{
				$return[] = "<script type=\"text/javascript\" src=\"custom/plugins/{$this->myName}/mediaelement-and-player.min.js\"></script>";
			}
			$return[] = "<script type=\"text/javascript\">
							$(document).ready(function() {
								$('audio, video').mediaelementplayer();
								});
							</script>";
		}
		// the colour picker
		if (ACTION == 'backend' && isset($_GET['page']) && $_GET['page'] == 'plugins' && isset($_GET['edit']) && $_GET['edit'] == 'mediaelement')
		{
			$return[] = "<script src=\"backend/jscolor/jscolor.js\" type=\"text/javascript\"></script>";
		}

		return $return;
	}
			
	public function onBackendPostingDataReady($fields)
	{
		$data = DataTables::AudioTypeData($fields['audio_type']);
		$return = array();

		if (isset($data['html5']) && $data['html5'] == true)
		{
			$this->MERequired = true;
			if ($fields['audio_type'] == 1 || $fields['audio_type'] == 2 || $fields['audio_type'] == 3) // audio file
			{
				$html = "<audio width=\"240\" height=\"30\" controls=\"controls\" preload=\"none\">\n
						<source type=\"{$data['mime']}\" src=\"{$fields['audio_link']}\" />\n";
						if(!empty($fields['addfiles']))
						{
							$addfiles = unserialize($fields['addfiles']);				
							foreach ($addfiles as $addfile)
							{
								$html .= "<source type=\"{$addfile['mime']}\" src=\"" . THIS_URL . '/audio/' . $addfile['name'] . "\" />\n";
							}
						}
				$html .= "</audio>";
			}
			else // video file
			{
				$html = "<video width=\"240\" height=\"200\" controls=\"controls\" preload=\"none\">\n
						<source type=\"{$data['mime']}\" src=\"{$fields['audio_link']}\" />\n";
						if(!empty($fields['addfiles']))
						{
							$addfiles = unserialize($fields['addfiles']);				
							foreach ($addfiles as $addfile)
							{
								$html .= "<source type=\"{$addfile['mime']}\" src=\"" . THIS_URL . '/audio/' . $addfile['name'] . "\" />\n";
							}
						}
				$html .= "</video>";
			}
			$return[] = array('plugin' 		=> $this->myName,
								'variable' 	=> 'fields',
								'offset' 	=> array('plugin_player'),
								'value' 	=> $html);
		}
		return $return;
	}

	public function onAllPageDataReady()
	{
		// the ME Player completely replaces the OnePixelOut player
		$return[] = array ('plugin' 	=> $this->myName,
							'variable' 	=> 'pixout_required',
							'offset' 	=> array(),
							'value' 	=> '');
		$return[] = array ('plugin' 	=> $this->myName,
							'variable' 	=> 'javascript',
							'offset' 	=> array('swfobject'),
							'value' 	=> '');
		$return[] = array ('plugin' 	=> $this->myName,
							'variable' 	=> 'javascript',
							'offset' 	=> array('pixout'),
							'value' 	=> '');

		if ($this->retainJWPlayer == false)
		{
			$return[] = array ('plugin' 	=> $this->myName,
								'variable' 	=> 'javascript',
								'offset' 	=> array('jwplayer'),
								'value' 	=> false);
		}
		return $return;	
	}

	private function checkColour($string)
	{
		$regex = "/^#(?:[0-9a-fA-F]{3}){1,2}|transparent|default|random$/";
		return  preg_match($regex, trim($string));
	}

	private function randomColour () {
	$c = '#';
	for ($i = 0; $i<6; $i++)
	    {
	     $c .=  dechex(rand(0,15));
	    }
	return $c;
	} 	
}
?>
