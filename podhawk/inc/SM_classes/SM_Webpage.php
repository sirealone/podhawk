<?php	

class SM_Webpage extends Smarty
{
	public $theme;

	function __construct($theme) // constructor
	{
        $this->Smarty(); // parent constructor

        $this->template_dir = PATH_TO_ROOT .'/podhawk/custom/themes/'.$theme;
        $this->compile_dir  = PATH_TO_ROOT .'/podhawk/smarty/templates_c/';
        $this->config_dir   = PATH_TO_ROOT .'/podhawk/custom/themes/'.$theme;
        $this->cache_dir    = PATH_TO_ROOT .'/podhawk/smarty/cache/';
		$this->compile_id   = $theme;
		
		$this->caching = true;
        $this->assign('app_name', 'ph_webpage');

		$this->theme = $theme;

		// define a new "common" resource for templates in the common templates directory
		$this->register_resource("common", array('SM_Webpage',
												"common_template",
                                       			"common_timestamp",
                                       			"common_secure",
												"common_trusted"
												));

		$this->register_resource('plugins', array('SM_Webpage',
												'plugin_template',
												'plugin_timestamp',
												'common_secure',
												'common_trusted'
												));

		// a default template handler to try to find a template even if it is in the wrong place
		$this->default_template_handler_func = array($this, 'find_template');

	}

	// function to help Smarty find a template even if it is in the wrong directory,
	// and to provide a default if no template can be found

	function find_template($type, $name, &$template_source, &$template_timestamp)
	{
		$theFile = '';

		// template cannot be found in the normal theme directory
		if ($type == 'file')
		{
			if (substr($name, 0, 7) != "common_") // add "common_" to the template name if necessary
			{
				$name = 'common_' . $name;
			}
			$theFile = PATH_TO_ROOT . "/podhawk/custom/themes/common_templates/$name";
		}
		// template cannot be found in the common_templates directory
		elseif ($type == 'common')
		{
			if (substr($name, 0, 7) == "common_") // remove "common_" from template name
			{
				$name = substr($name, 7, 0);
			}

			$reg = Registry::instance();

			$theme = $reg->findSetting('template');

			$theFile = PATH_TO_ROOT . "/podhawk/custom/themes/$theme/$name";
		}
		
		// try to get a template and a timestamp
		if (!empty($theFile) && is_readable($theFile))
		{
			$template_source = file_get_contents($theFile);

			$template_timestamp = filemtime($theFile);	
		}
		else // display a message
		{
			$template_source = "Sorry - I cannot find template $name";

			$template_timestamp = time();
		}					

        return true;
	}		


	// the following functions are needed to enable Smarty to recognise templates in the 'common_templates' directory
	static function common_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		$commonTemplate = PATH_TO_ROOT . "/podhawk/custom/themes/common_templates/common_$tpl_name";

		if (!file_exists($commonTemplate))
		{
			$commonTemplate = PATH_TO_ROOT . "/podhawk/custom/themes/common_templates/$tpl_name";
		}
		
		if (is_readable($commonTemplate))
		{
			$tpl_source = file_get_contents($commonTemplate);

			return true;
		}
		else
		{
			return false;
		}
	}

	static function common_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$commonTemplate = PATH_TO_ROOT . "/podhawk/custom/themes/common_templates/common_$tpl_name";

		if (!file_exists($commonTemplate))
		{
			$commonTemplate = PATH_TO_ROOT . "/podhawk/custom/themes/common_templates/$tpl_name";
		}

		if (is_readable($commonTemplate))
		{
		
			$tpl_timestamp = filemtime($commonTemplate);

			return true;
		}
		else
		{
			return false;
		}
	}

	static function common_secure($tpl_name, &$smarty_obj)
	{
		return true;
	}

	static function common_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}

	// similarly allow plugins to create their own templates
	static function plugin_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		$pluginTemplate = PLUGINS_DIR . $tpl_name;
	
		if (is_readable($pluginTemplate))
		{
			$tpl_source = file_get_contents($pluginTemplate);
			return true;
		}
		else
		{
			return false;
		}
	}

	static function plugin_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$pluginTemplate = PLUGINS_DIR . $tpl_name;
		
		if (is_readable($pluginTemplate))
		{
			$tpl_timestamp = filemtime($pluginTemplate);
			return true;
		}
		else
		{
			return false;
		}
	}
}			
?>
