<?php

class TR_TranslationBackend
{
	private $page;
	private $translationArray = array();
	private $reg;
	private $langFileLocation;

	public function __construct($page)
	{
		$this->page = $page;

		$this->reg = Registry::instance();

		$this->langFileLocation = PATH_TO_ROOT . '/podhawk/lang/';
	}
	
	public function setLangFileLocation($location)
	{
		$this->langFileLocation = $location;
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

	protected function findTranslationArray()
	{
		$firstTransArray = array();
		$secondTransArray = array();

		if (isset($_GET['language']) && in_array($_GET['language'], whitelist('backend_languages')))
		{	
			$langToUse = $_GET['language'];
		}
	
		else
		{
			$langToUse = $this->reg->findSetting('language');			
		}

		$firstTransArray = $this->readlangFile($langToUse);

		// merge the English language array, to pick up any keys which have not been translated
		if ($langToUse	!= 'english')
		{
			$secondTransArray = $this->readLangFile('english'); 
		}

		$transArray = array_merge($secondTransArray, $firstTransArray);

		return $transArray;
	}

	protected function readLangFile($lang)
	{
		$return = array();

		if (file_exists($this->langFileLocation . $lang . '.php')) // plugins may not always have a complete set of language files
		{
			include ($this->langFileLocation . $lang . '.php');
		}

		$array = "trans_" . $this->page;

		if (isset($$array))
		{
			if (isset($trans_common)) // if there is a 'common' translation array, we want it too
			{
				$return = array_merge($$array, $trans_common);
			}
			else $return = $$array;
		}
		elseif (isset($trans_common))
		{
			$return = $trans_common;
		}

		return $return;

	}		

	public function getAvailableLangs()
	{

	}
}
?>
