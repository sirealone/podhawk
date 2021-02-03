<?php

class SM_FrontendAjax extends SM_Webpage
{
   function __construct($theme, $ajaxAction)
   {
		parent::__construct($theme);
		
		$this->compile_id   = $theme . "-" . $ajaxAction;

		$this->caching = false;
		$this->assign('app_name', 'ph_frontend_ajax');
   }
}
	
?>
