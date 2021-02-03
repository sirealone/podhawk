<?php

abstract class PluginPattern
{

	// This is an abstract class. It cannot be instantiated. All plugins extend and implement this class.
	// It contains some abstract methods which must be implemented in each plugin class, and also methods
	// and properties which are available to all plugin classes.

	protected $myName;
	protected $myFullName;
	protected $version;
	protected $description;
	protected $author;
	protected $contact;
	protected $params = array();
	protected $listeners = array();
	protected $initialParams = array();
	protected $enabled;
	protected $run_order;
	protected $dataNeeded = array(); // parameters required from other enabled plugins
	protected $postingFooter = array(); //array of items to add to the end of postings, key = posting_id, value = html to  be added
	protected $settings = array();
	protected $reg; // Registry object
	protected $langFileLocation; // location of language files (if any)


	// every plugin object must contain a method to create a backend page for the plugin
	abstract protected function backendPluginsPage();

	// ... and a method to convert the $_POST values submitted from the backend page
	// into params for insertion in the database
	abstract protected function getParamsFromPosts();

	// Registering the plugin in the database
	public function setup()
	{
		$dosql = "INSERT INTO ". DB_PREFIX . "lb_plugins (name, full_name, run_order, enabled, params)
		VALUES (" . escape($this->myName) . ",
				" . escape($this->myFullName) . ",
				'3',
		 		'0',
				 " . escape(serialize($this->initialParams)) . ");";

		return $GLOBALS['lbdata']->Execute($dosql);
	}

	// Removing the plugin from the database
	public function remove()
	{
		$dosql = "DELETE FROM " . DB_PREFIX . "lb_plugins WHERE name = " . escape($this->myName);
		return $GLOBALS['lbdata'] -> Execute ($dosql);

	}

	// Returns the "event listeners" for the plugin
	public function registerListeners()
	{
		return $this->listeners;
	}

	// returns the full name of the plugin
	public function getFullName()
	{
		return $this->myFullName;
	}

	// returns data needed to create the backend page for the plugin
	public function getPluginPageData()
	{

		$p = new HT_Standard();

		$return['name'] = entity_encode($this->myName);
		$return['fullName'] = entity_encode($this->myFullName);
		$return['version'] = entity_encode($this->version);
		$return['author'] = entity_encode($this->author);
		$return['contact'] = entity_encode($this->contact);
		$return['description'] = $p->purify($this->description);
	
		$dosql = "SELECT enabled, run_order, params FROM " . DB_PREFIX . "lb_plugins WHERE name = " . escape($this->myName);
		$result = $GLOBALS['lbdata']->GetArray($dosql);

		$return['enabled'] = (!empty($result[0]['enabled'])) ? $result[0]['enabled'] : '0';
		$return['run_order'] = (!empty($result[0]['run_order'])) ? $result[0]['run_order'] : '3';
		$return['params'] = unserialize($result[0]['params']);

		$return['html'] = $this->backendPluginsPage(); // the html for the main part of the plugin page
		$return['html2'] = $this->backendPluginsPage2(); // the html for an optional second part of the plugin page

		return $return;
	}

	// updates the database entry for the plugin
	public function writeData()
	{
		//if (!isset($_POST['enable'])) $_POST['enable'] = '0';
		$params = $this->getParamsFromPosts();
	
		$dosql = "UPDATE " . DB_PREFIX . "lb_plugins SET
			enabled = '" . $_POST['enable'] .  "',
			run_order = '" .$_POST['run_order'] ."',
			params = " . escape(serialize($params)) . "
			WHERE name = " . escape($this->myName);

		$GLOBALS['lbdata']->Execute($dosql);

		$this->params = $params;
		$this->enabled = $_POST['enable'];
		$this->run_order = $_POST['run_order'];
	

	}

	// change the value of a single param and enter it in the database
	protected function changeParam ($offset, $value)
	{
	
		$this->params[$offset] = $value;
		$dosql = "UPDATE ". DB_PREFIX . "lb_plugins SET
		params = " . escape(serialize($this->params)) . "
		WHERE name = " . escape($this->myName);
		$GLOBALS['lbdata']->Execute($dosql);
	}

	// gets data needed for the summary plugins page		
	public function getData()
	{

		$dosql = "SELECT * FROM " .DB_PREFIX . "lb_plugins
			WHERE name = " . escape($this->myName);
		$result = $GLOBALS['lbdata']->GetArray($dosql);
		$return['enabled'] = $result[0]['enabled'];
		$return['params'] = unserialize($result[0]['params']);
		return $return;
	}

	// returns initial values for the plugin parameters (ie when the plugin is first activated)
	public function getInitialParams()
	{
		return $this->initialParams;
	}

	public function getTranslationArray($key='') // returns array of translation data for a backend page which has been added by the plugin
	{
		// if no $key is specified, return the array with the same name as the plugin
		$key = (empty($key)) ? $this->myName : $key;
		$t = new TR_TranslationBackend($key);
		$t->setLangFileLocation($this->langFileLocation); // tell $t that we want trans files from the plugin, not from the main 'lang' folder
		$return = $t->getTrans();
		return $return; 
	}	

	// returns a "shopping list" of the information which this plugin needs about other enabled plugins
	public function dataNeeded()
	{

		return $this->dataNeeded;

	}

	// the default is that there is no second part to the backend page.
	protected function backendPluginsPage2()
	{

		return "";
	
	}

	// if there are no user-configuarable parameters to display in the backend page
	protected function noUserParams()
	{

		return "<td colspan=3>There are no user-configurable parameters for this plugin.</td>";

	}

	// creates a list of available backend language options
	protected function getLanguageOptionsHTML ($lang_options, $lang)
	{

		$html = <<<ENDOFTEXT
	<tr>
		<td class="left">{$lang['language']}</td>
		<td class="center">
ENDOFTEXT;

	$html .= $this->makeOptions($lang_options, "language");

	$html .= <<<ENDOFTEXT
		</td>
		<td class="right">
		{$lang['what_lang']}
		</td>
	</tr>	
ENDOFTEXT;

	return $html;
		}

	protected function makeOptions ($array, $paramName)
	{

	// a convenient way of writing an html list of options.
	// $array is an array where the keys are the $_POST values which each option will send
	// and the value is the text for each option which will display in the form
	// $paramName is the name of the select box, and hence the name of the parameter to be stored in the database.
		$html = "<select name=\"$paramName\">\n";
		foreach ($array as $key => $text)
		{
	
			$selected = ($key == $this->params[$paramName]) ? " selected=\" selected\"" : "";	
			$html .= "<option value = \"" . $key . "\"" . $selected .">" . $text . "</option>\n";	
		}
		$html .= "</select>";
		return $html;
	}

	protected function makeCheckBox ($value, $paramName)
	{

	// a convenient way of making an html checkbox

		$checked = ($this->params[$paramName] == 1) ? " checked=\"checked\"" : "";
		$html = "<input type=\"checkbox\" name=\"" . $paramName . "\" value=\"" . $value . "\"" . $checked . " />";
		return $html;
	}

	protected function makeRadioButtons($paramName)
	{
		// creates a pair of yes/no radio buttons
		if ($this->params[$paramName] == true)
		{
			$checked_1 = "checked=\"checked\"";
			$checked_0 = "";
		}
		else
		{
			$checked_1 = "";
			$checked_0 = "checked=\"checked\"";
		}

		$html = "<input type=\"radio\" class= \"radio\" name=\"$paramName\" value=\"1\" $checked_1 />Yes &nbsp;&nbsp;
				<input type=\"radio\" class=\"radio\" name=\"$paramName\" value=\"0\" $checked_0 />No";
		return $html;
	}
		

	public function getPostingFooter($id)
	{

		return (isset($this->postingFooter[$id])) ? $this->postingFooter[$id] : false;

	}	

	protected function getSettings()
	{

		if (!isset($this->reg))
		{
			$this->reg = Registry::instance();
		}
		return $this->reg->getSettingsArray();

	}

	protected function getCategories()
	{
		if (!isset($this->reg))
		{
			$this->reg = Registry::instance();
		}
		return $this->reg->getCategoriesArray();
	}

	protected function getPlayers()
	{
		if (!isset($this->reg))
		{
			$this->reg = Registry::instance();
		}
		return $this->reg->getPlayers();
	}

	protected function getLangFileLocation()
	{
		return PLUGINS_DIR . $this->myName . '/lang/';
	}

	
}
?>
