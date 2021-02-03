<?php

class Plugins
{

	public $plugins; // array of plugin objects
	public $plugins_dir = array(); // contents of plugins directory
	public $pluginsData = array(); // array of data for all installed plugins
	public $enabledPlugins = array(); // array of names of enabled plugins
	public $disabledPlugins = array(); // array of names of non-enabled plugins
	public $metaTags;
	public $params = array();
	public $listeners = array();
	public $menuTransArray = array();
	public static $instance;
	private $debug = false;


	private function __construct ()
	{
		$this->plugins = new stdClass();

		$this->plugins_dir = get_dir_contents(PLUGINS_DIR);

		if (PH_VERSION > 1.69)
		{

			$dosql = "SELECT * FROM ". DB_PREFIX . "lb_plugins ORDER BY run_order ASC";
			$results = $GLOBALS['lbdata']->GetArray($dosql);

			foreach ($results as $result)
			{
				$name = $result['name'];

				// remove the plugin if it is not in the plugins directory
				if (!in_array($name, $this->plugins_dir))
				{

					$dosql = "DELETE from " . DB_PREFIX . "lb_plugins WHERE name = '" . $result['name'] ."';";
					$GLOBALS['lbdata']->Execute($dosql);

				}
				else
				{	 		
					// else create an array of plugin data
					$name = $result['name'];
					$result['params'] = unserialize($result['params']);
					$this->pluginsData[$name] = $result;		

					// create arrays of the names of enabled and non-enabled plugins
					if ($result['enabled'] == '1') $this->enabledPlugins[] = $result['name'];
					else $this->disabledPlugins[] = $result['name'];
		
					// for each plugin create an object and pass the relevant params to it	
					include PLUGINS_DIR . $name . "/" . $name . ".php";

					$this->plugins->$name = new stdClass();
					$this->plugins->$name = new $name($result);

					// create an array of event listeners for enabled plugins
					if ($this->pluginsData[$name]['enabled'] == '1')
					{
						$pluginListeners = $this->plugins->$name->registerListeners();

						foreach ($pluginListeners as $listener)
						{
							$this->listeners[$listener][] = $name;
						}
					}
				}
			}
	
		//ask plugins whether they require information about params of other enabled prugins
		$this->sendDataToPlugins();

		} // close version > 1.69

		if ($this->debug)
		{ 
			echo "<pre>Constructor<br />";
			echo "Plugins directory :<br />";
			print_r($this->plugins_dir);
			echo "All plugin data :<br />";
			print_r ($this->pluginsData);
			echo "Enabled plugins :<br />";	
			print_r ($this->enabledPlugins);
			echo "Non enabled plugins :<br />";
			print_r ($this->disabledPlugins);	
			echo "<br />Listeners :<br />";
			print_r ($this->listeners);
			echo "</pre>";
		}
	}

	static public function instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	function event($event, $data=null)
	{

		$return = array();

		if (isset($this->listeners[$event]))
		{

			foreach ($this->listeners[$event] as $plugin_name)
			{
				$returned_data = $this->plugins->$plugin_name->$event($data);

				if ($returned_data)
				{
					foreach ($returned_data as $item)
					{
						$return[] = $item;

					}
				}	
			}
		}

		if ($this->debug)
		{
			echo "<pre>Returned by " . $event . "<br />";
			print_r ($return);
			echo "</pre>";
		}	
		return $return;

	}

	public function sendDataToPlugins ()
	{
		//send to plugins information about parameters of other enabled plugins
		foreach ($this->pluginsData as $name=>$data)
		{	
			$dataNeeded = $this->plugins->$name->dataNeeded();

			foreach ($dataNeeded as $item)
			{
				$otherPlugin = $item['other_plugin'];
				$paramNeeded = $item['param'];

				if (isset($this->pluginsData[$otherPlugin]['params'][$paramNeeded])
				&& $this->pluginsData[$otherPlugin]['enabled'] == '1')
				{

					$valueNeeded = $this->pluginsData[$otherPlugin]['params'][$paramNeeded];
					$dataToSend = array($otherPlugin, $paramNeeded, $valueNeeded);
			
					$this->plugins->$name->receiveData($dataToSend);
				}
			}
			
		}
	}

	public function makePostingFooter($id)
	{
		// collect posting Footer items from plugins and create a string which the main programme can then add to the posting text.
		$string = "";

		foreach ($this->enabledPlugins as $name)
		{
		
			$item = $this->plugins->$name->getPostingFooter($id);

			if (!empty($item))
			{
				$string .= $item . "&nbsp;";
			}

		}

		if (!empty($string))
		{

			return "<p class=\"postingFooter\">" . trim($string) . "</p>";

		}

		else return false;

	}

	public function getProperty($plugin, $property)
	{
		return $this->plugins->$plugin->$property;
	}
		
	function debug()
	{

		$this->debug = true;

	}

	function returnListeners()
	{

		return $this->listeners;

	}

	function getFullName($plugin)
	{
		$n = $this->plugins->$plugin->getFullName();
		return $n;
	}

	public function enabled($name)
	{
		return (in_array($name, $this->enabledPlugins));
	}

	public function getParam ($pluginName, $paramName)
	{
	// NB this method returns the param value at the time the plugin object was initialised.
	// use it only in scripts which do not themselves change any plugin parameters.
		$return = (isset($this->pluginsData[$pluginName]['params'][$paramName])) ? $this->pluginsData[$pluginName]['params'][$paramName] : false;
		return $return;
		
	}
}
?>
