<?php

class slideshow extends PluginPattern
{
	private $lightboxNeeded = false;

	public function __construct($data=NULL)
	{
		
		$this->myName = "slideshow";
		$this->myFullName = "Slideshow";
		
		$this->version = "1.0";
		$this->author = "Peter Carter";
		$this->contact = "cpetercarter@googlemail.com";

		$this->langFileLocation = $this->getLangFileLocation();
		$this->trans = $this->getTranslationArray($this->myName);

		$this->description = $this->trans['description'];

		$this->initial_params = array();

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
		$this->enabled = (!empty($data['enabled'])) ? $data['enabled'] : '0';

		$this->listeners = array('onCreateMenu', 'registerBackendPages', 'addCSS', 'addHeadScript', 'onPostingDataReady', 'onBackendPostingDataReady');
	}

	// there are no user defined parameters for this plugin..
	protected function backendPluginsPage()
	{
		return $this->noUserParams();
	}

	//...and therefore no $_POST variables to convert into parameters
	protected function getParamsFromPosts()
	{
		return array();
	}

	public function onCreateMenu()
	{
		$return = array();

		$t = new TR_TranslationBackend('menu');
		$transPostings = $t->getTrans('p');
		$tt = new TR_TranslationBackend('common');
		$transNav = $tt->getTrans('nav');
		$transMenu = $this->getTranslationArray('menu');		

		if (!empty($transPostings))
		{
			$return[] = array (	'plugin' 	=> $this->myName,
								'variable' 	=> 'menu_array',
								'offset' 	=> array($transPostings, $transMenu['slideshow']),
								'value' 	=> 'index.php?page=slideshow'
								);
		}
		if (!empty($transNav))
		{
			$return[] = array (	'plugin' 	=> $this->myName,
								'variable' 	=> 'non_admin_menu',
								'offset' 	=> array($transNav, $transMenu['slideshow']),
								'value' 	=> 'index.php?page=slideshow'
								);
		}
		return $return;
	}

	public function registerBackendPages()
	{
		$return = array();
		$return[] = array(	'plugin' => $this->myName,
							'page_name' => 'slideshow');
		return $return;
	}

	public function addCSS()
	{
	// add stylesheet for the slideshow page, and the lightbox css file
		$return = array();

		if (ACTION == 'backend' && isset($_GET['page']) && ($_GET['page'] == 'slideshow' || ($_GET['page'] == 'record2' && $this->lightboxNeeded)))
		{
			$return = array(	"<link rel=\"stylesheet\" type=\"text/css\" href=\"custom/plugins/{$this->myName}/{$this->myName}.css\" />",
								"<link rel=\"stylesheet\" type=\"text/css\" href=\"lib/lightbox/lightbox.css\" media=\"screen\" />");
		}

		return $return;
	}

	public function addHeadScript()
	{
	// add the js functions file for backend slideshow page and lightbox.js, and lightbox.js when record2 page contains a slideshow
		$return = array();

		if (ACTION == 'backend' && isset($_GET['page']) && ($_GET['page'] == 'slideshow' || ($_GET['page'] == 'record2' && $this->lightboxNeeded)))
		{
			if($_GET['page'] == 'slideshow')
			{
				$return[] = "<script type=\"text/javascript\" src=\"custom/plugins/{$this->myName}/{$this->myName}.js\"></script>";
			}

			$return[] =  "<script type=\"text/javascript\" src=\"lib/lightbox/lightbox.js\"></script>";
		}
		return $return;
	}

	public function onPostingDataReady($postings)
	{
			$return = array();

			foreach ($postings as $key => $posting)
			{
				if ($posting['audio_type'] == '24')
				{
					$xml = AUDIOPATH . $posting['audio_file'];

					$html = Plugin__slideshow__functions::makeSlideshowHTML($xml);;

					$return[] = array(	'plugin' => $this->myName,
										'variable' => 'postings',
										'offset' => array($key, 'plugin_player'),
										'value' => $html);
				}
			}
			return $return;
	}

	public function onBackendPostingDataReady($fields)
	{
		$return = array();

		if ($fields['audio_type'] == '24')
		{
			$xml = AUDIOPATH . $fields['audio_file'];

			$html = Plugin__slideshow__functions::makeSlideshowHTML($xml);;

			$return[] = array(	'plugin' => $this->myName,
								'variable' => 'fields',
								'offset' => array('plugin_player'),
								'value' => $html);

			$this->lightboxNeeded = true;
		}
		return $return;
	}
}
?>
