<?php

class TR_WebpageMetatags
{
	private $reg;
	private $theme;
	private $webpageLanguage = 'en';

	public function __construct($theme, $plugins)
	{
		$this->theme = $theme;
	
		$this->plugins = $plugins;

		$this->reg = Registry::instance();
	}

	public function setWebpageLanguage($lang)
	{
		$this->webpageLanguage = $lang;
	}

	public function getStandardTags($summary)
	{
		$metatags = array(
			"charset" => "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />",
			'language' => "<meta http-equiv=\"content-language\" content=\"{$this->webpageLanguage}\" />",
			'generator' => "<meta name=\"generator\" content=\"Podhawk\" />");

		if (isset($_GET['id']) && !empty($summary))
		{
			$description_tag = $summary;
		}
		else
		{
			$description_tag = $this->reg->findSetting('description');
		}

		$metatags['description'] = "<meta name=\"description\" content=\"$description_tag\" />";		

		//then get metatags from plugins
		$metatags = array_merge($metatags, $this->plugins->event('addMetatags'));

		return $metatags;

	}

	public function getStyleSheets($lightboxRequired)
	{
		$css['screen'] = "<link rel=\"stylesheet\" media=\"screen\" href=\"podhawk/custom/themes/{$this->theme}/screen.css\" type=\"text/css\" />";
		$css['images'] = "<link rel=\"stylesheet\" media=\"screen\" href=\"podhawk/custom/themes/common_templates/images.css\" type=\"text/css\" />";
		if ($lightboxRequired)
		{
			$css['lightbox'] = "<link rel=\"stylesheet\" media=\"screen\" href=\"podhawk/lib/lightbox/lightbox.css\" type=\"text/css\" />";
		}
		

		//get any other style sheets from plugins
		$css = array_merge($css, $this->plugins->event('addCSS'));

		return $css;
	}

	public function getJavascript($pixout, $jw_player_required, $lightboxRequired)
	{
		//standard calls
		$javascript = array("jquery" => JQUERY_LOCATION,
							"ajax"   => "podhawk/ajax/functions.frontend_ajax.js");

		if ($pixout) // we need swfobject and the audio-player js file to load the One Pixel Out player
		{
			$javascript['swfobject'] ='http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js';
			$javascript['pixelout'] = 'podhawk/custom/players/onepixelout/audio-player-uncompressed.js';	
		}

		$jw_player_installed = jwplayer_installed();

		if ($jw_player_required && $jw_player_installed) // JW player needs its own loader
		{
			$javascript['jwplayer'] = 'podhawk/custom/players/jwplayer/jwplayer.js';
		}

		if ($lightboxRequired)
		{
			$javascript['lightbox'] = 'podhawk/lib/lightbox/lightbox_frontend.js';
		}

		// add TinyMCE js if needed for comments on a single posting page, if we are using loudblog or akismet anti-spam
		if 	(isset($_GET['id']) &&
			$this->reg->findSetting('comment_text_editor') == TRUE &&
			($this->reg->findSetting('acceptcomments') == 'loudblog' || $this->reg->findSetting('acceptcomments') == 'akismet'))
		{
			$javascript['mce'] 		= 'podhawk/tiny_mce/tiny_mce.js';
			$javascript['mce_init'] = 'podhawk/tiny_mce/comments_editor.js';
		}

		//add any theme-specific javascript
		if (file_exists(PATH_TO_ROOT . "/podhawk/custom/themes/{$this->theme}/theme.js"))
		{
			$javascript['theme'] =  "podhawk/custom/themes/{$this->theme}/theme.js";
		}

		return $javascript;
	}

	public function getNameSpaces()
	{
		$namespaces = array('W3' => "html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"" . $this->webpageLanguage . "\" lang=\"" . $this->webpageLanguage ."\"");

		//any other namespaces from plugins?
		$namespaces = array_merge($namespaces, $this->plugins->event('addNamespace'));

		return $namespaces;
	}
}
?>
