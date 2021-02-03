<?php

class multi_themes extends PluginPattern
{
	private $linkedCategory = ''; // the ID of a category which we have linked to a theme

	function __construct($data=null)
	{
		$this->myName = 'multi_themes';
		$this->myFullName = 'Multiple Themes Plugin';
		$this->version = '1.0';

		$this->langFileLocation = $this->getLangFileLocation();

		$this->trans = $this->getTranslationArray($this->myName);

		$this->description = $this->trans['description'];

		$this->author = 'Peter Carter';
		$this->contact = 'cpetercarter@googlemail.com';
		$this->initialParams = array('themeAssoc' => array());

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
		$this->enabled = $data['enabled'];

		$this->listeners = array("onInitialise", "onRedirect", "onPostingDataReady");

		$this->reg = Registry::instance();
	}

	protected function backendPluginsPage()
	{
		$catsList = $this->reg->getCategorynames();
		$catsList[999] = $this->trans['all_single'];

		$themesList = $this->getThemes();

		$index = 0;

		$html = "<tr>
				<td colspan=\"4\" class=\"right\">{$this->trans['choose_category']}</td>
				</tr>";
		if (!empty($this->params['themeAssoc']))
		{
			$html .= "<tr>
					<td colspan=\"4\" class=\"right\">{$this->trans['existing_links']}</td>
					</tr>";
		

			foreach ($this->params['themeAssoc'] as $categoryID => $associatedData)
			{
				$html .= <<<EOF
				<tr>
					<td class="left">{$this->trans['category']} :<br />
						<select name="category[$index]" style="width:140px;">
EOF;
				foreach ($catsList as $id => $name)
				{
					if ($id == $categoryID)
					{
						$html .= "<option value=\"$id\" selected=\"selected\">$name</option>";
					}
					else
					{
						$html .= "<option value=\"$id\">$name</option>";
					}
				}
				$html .= <<<EOF
						</select>
					</td>
					<td class="center">{$this->trans['theme']}: <br />
						<select name="theme[$index]" style="width:140px;">
EOF;
				foreach ($themesList as $theme)
				{
					if ($theme == $associatedData['theme'])
					{
						$html .= "<option value=\"$theme\" selected=\"selected\" >$theme</option>";
					}
					else
					{
						$html .= "<option value=\"$theme\">$theme</option>";
					}
				}

				$playerWidth = (empty($associatedData['width'])) ? '' : $associatedData['width'];
			
				$html .= <<<EOF
						</select>
					</td>
					<td>{$this->trans['jw_width']}: <br />
						<input type="text" name="playerWidth[$index]" value="$playerWidth" style="width:120px;"/> px<br />
					</td>
					<td>
						{$this->trans['remove']} :<br />
						<input type="checkbox" value="Delete" name="delete[$index]" />
					</td>
				</tr>
EOF;
				$index ++;
			}
		} // close "if we have themeAssoc"

		// extra row for a new entry
			$html.= "
				<tr>
					<td colspan=\"4\" class=\"right\">{$this->trans['add_new']}:</td>
				</tr>
				<tr>
					<td class=\"left\">{$this->trans['category']}:<br />
					<select name=\"category[$index]\" style=\"width:140px;\">
					<option value=\"\">{$this->trans['choosecat']}</option>";

			foreach ($catsList as $id => $name)
			{
				$html .= "<option name=\"category[]\" value=\"$id\">$name</option>";
			}
			$html .= <<<EOF
					</select>
					</td>
					<td class="center">{$this->trans['theme']}:
						<select name="theme[$index]" style="width:140px;">
						<option value="">{$this->trans['choose_theme']}</option>
EOF;
			foreach ($themesList as $theme)
			{
				$html .= "<option value=\"$theme\">$theme</option>";
			}
			$html .= <<<EOF
					</select>
					</td>
					<td>{$this->trans['jw_width']}: <br />
					<input type="text" name="playerWidth[$index]" value="" style="width:120px;"> px
					</td>
					<td></td></tr>
EOF;

		return $html;
	}						
	

	protected function getParamsFromPosts()
	{
		$params = array();

		if (isset($_POST['delete']))
		{
			foreach ($_POST['delete'] as $index => $value)
			{
				unset ($_POST['category'][$index]);
			}
		}

		foreach ($_POST['category'] as $index => $category)
		{
			if (!empty($category) && !empty($_POST['theme'][$index])) // we must have both a category and a theme
			{
				$params['themeAssoc'][$category] = array('theme' => $_POST['theme'][$index],
														'width' => $_POST['playerWidth'][$index]);
			}
		}

		return $params;
	}

	public function onInitialise()
	{
		if (ACTION == 'webpage')
		{
			$pagination = new PO_Pagination_Webpage();

			$rows = $pagination->getRows();

			if (!empty($rows)) // have we actually found any postings?
			{
				// we build up an array of arrays
				foreach ($rows as $row)
				{
					// first the category id's associated with each posting
					$idArray = array($row['category1_id'], $row['category2_id'], $row['category3_id'], $row['category4_id']);

					if (isset($_GET['id'])) // add '999' if this is a single posting
					{
						$idArray[] = '999';
					}
					$catArray[] = $idArray;
				}
				$catArray[] = array_map('strval', array_keys($this->params['themeAssoc'])); // add array of cat ids which this plugin links to themes
					
				$commonCategories = call_user_func_array('array_intersect', $catArray); // identify any common category ids

				if (!empty($commonCategories))
				{
					if (in_array('999', $commonCategories)) // give priority to a rule for single posts, if there is one
					{
						$this->linkedCategory = '999';
					}
					else // else choose the first common category
					{
						$this->linkedCategory = $commonCategories[0]; 
					}
				}
		
				if (!empty($this->linkedCategory)) // has the plugin identified a theme?
				{
					$_GET['theme'] = $this->params['themeAssoc'][$this->linkedCategory]['theme'];
				}
			}
		}		
	}

	public function onRedirect($requested)
	{
		if (!isset($_GET)) // another plugin may already have succeeded in rewriting...
		{
			$rewriter = new Rewriter($requested); // ...but if not, try to generate some useful GETs from the requested address

			$rewriter->rewrite();
		}

		if (isset($_GET)) //..and if we succeed, test whether any of our theme/category associations apply
		{
			$this->onInitialise();
		}
	}

	public function onPostingDataReady($postings)
	{
		$return = array();

		if (!empty($this->linkedCategory))
		{
			$width = $this->params['themeAssoc'][$this->linkedCategory]['width'];

			if (!empty($width)) // if we haven't set a new width, there is no need to change the jw_vars.
			{
				$playersData = $this->reg->getPlayers();
			
				$ratio = $playersData['jw_video_height']/$playersData['jw_video_width'];

				$height = round($width * $ratio);
			
				foreach ($postings as $key => $posting)
				{
					if ($posting['playertype'] == 'jwvideo') // we only adjust the size of the video player
					{
						$jw_vars = json_decode($posting['jw_vars'], true); // decode the jw_vars into an array

						if (!empty($jw_vars))
						{
							$playlistArray = array(22,23,31,33); // if we have a playlist, we adjust the player size

							if (in_array($posting['audio_type'], $playlistArray))
							{
								if ($posting['audio_type'] == 22 && $posting['filelocal'] == true) // if we have a local playlist, does it contain only mp3s?
								{
									$xml = new XM_Playlist(AUDIOPATH . $posting['audio_file']);
		
									if ($xml->allMP3()) // if so, the player's basic height should be the height of the audio player
									{
										$playersData['jw_video_height'] = $playersData['jw_audio_height'];
									}
								}

								if ($playersData['jw_playlist'] == 'bottom') // add to the height if the playlist is below the player..
								{
									$jw_vars['height'] = $height + $playersData['jw_playlistsize'];
									$jw_vars['width'] = $width;
								}

								if ($playersData['jw_playlist'] == 'right') // .. or to the width if the playlist is to the right of the player
								{
									$jw_vars['width'] = $width + $playersData['jw_playlistsize'];
									$jw_vars['height'] = $height;
								}
							}
							else // we use the width/height that we calculated earlier
							{
								$jw_vars['width'] = $width;
								$jw_vars['height'] = $height;
							}
						
							$jw_vars_encoded = str_replace("\\", '', json_encode($jw_vars));

							$return[] = array ('plugin' => $this->myName,
												'variable' => 'postings',
												'offset' => array($key, 'jw_vars'),
												'value' => $jw_vars_encoded);
						} // close 'if !empty ($jw_vars)
					} // close if jw_video
				} // close postings loop
			} // close 'have we set a new width'				
		}// close if we have a linked category
		return $return;
	}

	private function getThemes()
	{
		$themes_contents = get_dir_contents('custom/themes');
		foreach ($themes_contents as $content)
		{
			//ignore hidden or back-up files
			if (substr($content,0,1) == "." || substr($content,-1) == "~") continue;

			//ignore the 'common_templates' directory
			if (trim($content) == 'common_templates') continue;

			$themesList[] = $content;
		}

		natcasesort ($themesList);

		return $themesList;
	}
}
?>
