<?php

	// insert additional settings for new Facebook authorisation method
	$settings_array = array("fb_app_id" 		=> '',
							"fb_canvas_page" 	=> '');

	foreach ($settings_array as $name=>$value)
	{
		$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('".$name."', '".$value."');";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	// add itunes_explicit column to postings table
	if (DB_TYPE == 'mysql')
	{
		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ADD itunes_explicit INT(2) DEFAULT '0'";
		$GLOBALS['lbdata']->Execute($dosql);		
	}

	elseif (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7')
	{
		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ADD COLUMN itunes_explicit INTEGER DEFAULT '0'";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	elseif (DB_TYPE == "sqlite")
	{
		include INCLUDE_FILES .'/updates/update_functions.php';

		sqlite_add_columns('postings', 'itunes_explicit INT(2)', "'0'");
	}
?>		
