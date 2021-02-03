<?php

class ph_facebook extends Smarty
{

	function ph_facebook()
	{	
		$this->Smarty();
		$this->template_dir 	= ""; //the facebook template is in the facebook directory
		$this->compile_dir		= PATH_TO_ROOT . "/podhawk/smarty/templates_c/";
		$this->config_dir   	= "";
        $this->cache_dir    	= PATH_TO_ROOT . '/podhawk/smarty/cache/';
		$this->compile_id   	= ACTION;

        $this->caching 			= true;
        $this->assign('app_name', 'ph_facebook');
	}

}

?>
