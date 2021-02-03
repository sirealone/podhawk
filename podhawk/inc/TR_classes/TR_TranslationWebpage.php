<?php

class TR_TranslationWebpage
{
	private $translationArray = array();
	private $templateLang = 'english';
	private $theme;
	private $reg;
	private $themesDir;

	public function __construct($theme)
	{
		$this->reg = Registry::instance();

		if ($this->reg->findSetting('template_language'))
		{
			$this->templateLang = $this->reg->findSetting('template_language');
		}

		$this->theme = $theme;

		$this->themesDir = PATH_TO_ROOT . "/podhawk/custom/themes/";

	}

	public function getTrans($key=false)
	{
		if (empty($this->translationArray))
		{
			$this->translationArray = $this->findTranslationArray();
		}

		if (!$key)
		{
			return $this->translationArray;
		}
		elseif (isset($this->translationArray[$key]))
		{
			return $this->translationArray[$key];
		}
		else return '';
	}

	private function findTranslationArray()
	{
		
		$firstTransArray = array();
		$secondTransArray = array();
		$thirdTransArray = array();
	
		$reg = $this->reg; // language arrays need the reg object with name $reg!

		// is there a translation file in the theme directory?
		if (file_exists($this->themesDir . $this->theme . "/lang/" . $this->templateLang . ".php"))
		{
			include $this->themesDir . $this->theme . "/lang/" . $this->templateLang . ".php";
			$firstTransArray = $trans;	
		}

		// is there a translation file in the common_templates directory?
		if (file_exists($this->themesDir . "common_templates/lang/". $this->templateLang . ".php"))
		{
			include $this->themesDir. "common_templates/lang/" . $this->templateLang . ".php";
			$secondTransArray = $trans;
		}
		
		include $this->themesDir . "common_templates/lang/english.php";
		$thirdTransArray = $trans;

		// merge the three translation arrays - $firstTransArray overwrites $secondTransArray which
		// overwrites $thirdTransArray - ie trans file in theme folder takes priority. Any untranslated 
		// terms (ie not in either templateLang folder) are rendered in English.
		$translationArray = array_merge($thirdTransArray, $secondTransArray, $firstTransArray);
		

		return $translationArray;
	}

	public function getAvailableLangs()
	{
		$langs = (file_exists($this->themesDir . $this->theme . "/lang")) ? get_dir_contents($this->themesDir . $this->theme . "/lang") : array();

		$commonLangs = get_dir_contents($this->themesDir . "common_templates/lang");

		// merge the languages
		$langs = array_merge($langs, $commonLangs);

		// remove duplicates
		$langs = array_unique($langs);

		$theme_langs = array();

		foreach ($langs as $lang)
		{
			//ignore hidden or back-up files
			if (substr($lang,0,1) == "." || substr($lang,-1) == "~") continue;
			$lang = substr($lang,0,-4);

			$theme_langs[] = $lang;			
		}
		
		return $theme_langs;
	}
}

?>
