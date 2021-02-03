<?php

class short_text extends PluginPattern
{

	// this class extends and implements the abstract class PluginPattern (in podhawk/inc/classes)

	function __construct($data=null)
	{

		$this->myName = "short_text"; // the name by which PodHawk will know this plugin
		$this->myFullName = "Short Posting Text"; // a "human readable" name for the plugin
		$this->version = "1.1";
		$this->description = "With this plugin, you can cut the text of your postings on pages which list multiple postings; and show the full text on pages with a single posting. This version of the plugin requires PodHawk 7.1 or later."; // a description of what the plugin does
		$this->author = "Peter Carter";
		$this->contact = "cpetercarter@googlemail.com";
		// a set of default parameters for the plugin (eg for when the plugin is first enabled
		$this->initialParams = array("divider"=> "CUT_HERE", "link_text"=>"more", "language"=>"english");

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
		$this->enabled = $data['enabled'];

		// an array of the event listeners to which the plugin needs to respond
		$this->listeners = array('onPostingDataReady');

	}

	// this method implements the abstract method PluginPattern::backendPluginsPage()

	protected function backendPluginsPage()
	{

		$divider = entity_encode($this->params['divider']);
		$link_text = entity_encode($this->params['link_text']);
		$html = "";
		$lang = $this->getPluginsPageTranslationArray($this->params['language']);

		$lang_options = array("english" => "English", "deutsch" => "Deutsch");

		if (count($lang_options) > 1)
		{
			$html .= $this->getLanguageOptionsHTML ($lang_options, $lang);
		}

		$html .= <<<ENDOFTEXT
	<tr>
		<td class="left">{$lang['divider']}</td>
		<td class="center">
		<input type="text" name="divider" value="$divider" />
		</td>
		<td class="right">{$lang['divider_help']}</td>
	</tr>
	<tr>
		<td class="left">{$lang['link_text']}</td>
		<td class="center">
		<input type="text" name="link" value="$link_text" />
		</td>
		<td class="right">{$lang['link_text_help']}</td>
	</tr>
ENDOFTEXT;

		return $html;
	}

	// this method implements the abstract method PluginPattern::getParamsFromPosts()
	protected function getParamsFromPosts()
	{

		$params['divider'] 		= entity_encode($_POST['divider']);
		$params['link_text'] 	= entity_encode($_POST['link']);
		$params['language'] 	= $_POST['language'];

		return $params;

	}
	
	// when the plugin receives the "onPostingDataReady" event message, it requests changes in the posting data
	// for a multipost page, it will truncate the posting text at the specified "divider", and close any open html tags.
	// for any other page, it will strip out the divider

	public function onPostingDataReady($postings)
	{

		$return = array();	

		foreach ($postings as $key => $posting)
		{
	
			// no action if the posting text contains no diivider
			if (!strpos($posting['message_html'], $this->params['divider'])) continue;

			// for multipost web pages
			elseif (ACTION == 'webpage' && !isset($_GET['id']))
			{ 
				$text = $this->makeShortText($posting['message_html'], $key, $posting['permalink']);
			} 

			// for feed, single post webpages etc, simply strip out the divider
			else
			{
				$text = str_replace ($this->params['divider'], "", $posting['message_html']);
			}

			$return[] = array("plugin" => $this->myName,"variable"=> "postings", "offset" => array($key, "message_html"), "value" => $text);

		} 

		// return an array of changes, or a blank array if no changes are needed
		return $return;
	
	}

	// a translation array for the plugin backend page
	private function getPluginsPageTranslationArray($l)
	{

		$lang = array();

		$lang['english'] = array(
			'divider' => 'Divider :',
			'divider_help' => 'Multi-post pages will display only the first part of your posting, up to this divider, with a link to a page with the full text.',
			'language' => 'Language :',
			'link_text' => 'The text for a link to your posting.',		
			'what_lang' => 'What language do you want this Plugin to speak?',
			'link_text_help' => 'for example, "...read more here"' );

		$lang['deutsch'] = array(
			'divider' => 'Divider :',
			'divider_help' => 'Multi-Post-Seiten zeigt nur den ersten Teil Ihrer Entsendung bis zu diesem Divider, mit einem Link zu einer Seite mit dem gesamten Text.',
			'language' => 'Sprache :',
			'link_text' => 'Der Text für einen Link zu Ihrer Entsendung.',
			'what_lang' => 'Welche Sprache möchten Sie dieses Plugin, um zu sprechen?',
			'link_text_help' => 'zum Beispiel "... mehr lesen Sie hier"' );

		return $lang[$l];
	 }

	public function makeShortText($text, $key, $link='')
	{
		if (empty($link)) $link = 'index.php?id=' . $key;

		$addString = "<p><a href=\"" . $link . "\">" . $this->params['link_text'] . "</a></p>";
		
		$bits = explode ($this->params['divider'], $text);
		$string = $bits[0];

		$requiredLength = strlen(strip_tags($string)); // HT_Shortener wants the length as the number of text characters, excluding html tags

		$s = new HT_Shortener($text, $requiredLength);

		$s->setLastSpaceCut(false);

		$s->setAddString($addString, 'out');

		return $s->getShortText();

	}

}
?>
