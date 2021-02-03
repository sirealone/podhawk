<?php

	$settings_array = array('facebook' 		=> 0,
							'fb_api_key' 	=> '',
							'fb_secret' 	=> '',
							'fb_app_name' 	=> '',
							'fb_homepage' 	=> 0);

	foreach ($settings_array as $name=>$value)
	{

		$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('".$name."', '".$value."');";
		$GLOBALS['lbdata']->Execute($dosql);
		
	}
?>
