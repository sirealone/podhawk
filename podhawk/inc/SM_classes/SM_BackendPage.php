<?php

class SM_BackendPage extends Smarty
{
	function __construct()
	{
		$this->Smarty(); // parent constructor

		$this->template_dir = PATH_TO_ROOT . '/podhawk/smarty/templates/';
		$this->compile_dir  = PATH_TO_ROOT . '/podhawk/smarty/templates_c/';
		$this->config_dir   = PATH_TO_ROOT . '/podhawk/smarty/configs/';
		$this->cache_dir    = PATH_TO_ROOT . '/podhawk/smarty/cache/';
		$this->compile_id   = 'backend';

		$this->caching = false;
		$this->assign('app_name', 'ph_backend');
   	}
}
