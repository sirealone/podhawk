<?php

	// we need to increase the size of the params column in the plugins table
	//(to accomodate Yahoo authoisation tokens which are over 600 characters long!)
	if (DB_TYPE == 'mysql')
	{
		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_plugins MODIFY params TEXT";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	elseif (DB_TYPE == 'postgres8')
	{
		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_plugins ALTER COLUMN params TYPE TEXT";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	elseif (DB_TYPE == 'postgres7') //'ALTER COLUMN' doesn't work in postgres 7!
	{
		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_plugins ADD COLUMN temp TEXT";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "UPDATE " . DB_PREFIX . "lb_plugins SET temp = params";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_plugins DROP COLUMN params";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_plugins RENAME COLUMN temp TO params";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	else // sqlite
	{
		$dosql = "CREATE TABLE temp AS SELECT * FROM " . DB_PREFIX . "lb_plugins";

		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "DROP TABLE " . DB_PREFIX . "lb_plugins";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "CREATE TABLE " . DB_PREFIX . "lb_plugins (
			id INT(4) PRIMARY KEY,
			name VARCHAR(64) DEFAULT NULL,
			full_name VARCHAR(64) DEFAULT NULL,
			enabled INT(3) DEFAULT '0',
			run_order INT(2) DEFAULT 3,
			params TEXT )";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "INSERT INTO " . DB_PREFIX . "lb_plugins SELECT * FROM temp";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "DROP TABLE temp";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	// add summary column to the postings table
	if (DB_TYPE == 'mysql')
	{
		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ADD summary TEXT";
		$GLOBALS['lbdata']->Execute($dosql);		
	}

	elseif (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7')
	{
		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ADD COLUMN summary TEXT";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	elseif (DB_TYPE == "sqlite")
	{
		include INCLUDE_FILES . '/updates/update_functions.php';
		sqlite_add_columns('postings', 'summary TEXT', "''");
	}

	// add autosave setting
	$dosql = "INSERT INTO " . DB_PREFIX . "lb_settings (name, value) VALUES ('autosave', 0)";
	$GLOBALS['lbdata']->Execute($dosql);

	// remove unneeded facebook settings
	$fb_array = array('facebook','fb_api_key','fb_secret','fb_app_name','fb_homepage','fb_app_id','fb_canvas_page');
	foreach ($fb_array as $name)
	{
		$dosql = "DELETE FROM ". DB_PREFIX . "lb_settings WHERE name = '$name'";
		$GLOBALS['lbdata']->Execute($dosql);
	} 
?>
