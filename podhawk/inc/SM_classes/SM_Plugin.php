<?php

class SM_Plugin extends Smarty
{

	function __construct($plugin)
	{	
		$this->Smarty();

		$this->template_dir = PLUGINS_DIR . $plugin . "/";
		$this->compile_dir  = PATH_TO_ROOT .'/podhawk/smarty/templates_c/';
		$this->config_dir   = PLUGINS_DIR . $plugin . "/";
		$this->cache_dir    = PATH_TO_ROOT . '/podhawk/smarty/cache/';
		$this->compile_id   = "plugin_" . $plugin;

		$this->caching = false;
		$this->assign('app_name', 'ph_plugin');

		if (ACTION == 'backend')
		{
			// tell Smarty where to find standard backend templates such as head.tpl and footer.tpl
			$this->register_resource("standard", array('SM_Plugin',
												"standard_template",
                                       			"standard_timestamp",
                                       			"standard_secure",
												"standard_trusted"
												));
		}
	}

	static function standard_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		$standardTemplate = PATH_TO_ROOT . "/podhawk/smarty/templates/$tpl_name";
		
		if (is_readable($standardTemplate))
		{
			$tpl_source = file_get_contents($standardTemplate);

			return true;
		}
		else
		{
			return false;
		}
	}

	static function standard_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$standardTemplate = PATH_TO_ROOT . "/podhawk/smarty/templates/$tpl_name";

		if (is_readable($standardTemplate))
		{		
			$tpl_timestamp = filemtime($standardTemplate);

			return true;
		}
		else
		{
			return false;
		}
	}

	static function standard_secure($tpl_name, &$smarty_obj)
	{
		return true;
	}

	static function standard_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}	   
}
?>
