<?php

	// remove the access table
	$dosql = "DROP table IF EXISTS " . DB_PREFIX . "lb_access";
	$GLOBALS['lbdata']->Execute($dosql);

	// change sessions:session_data from VARCHAR to TEXT
	if (DB_TYPE == 'mysql')
	{
		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_sessions CHANGE session_data session_data TEXT";
		$GLOBALS['lbdata']->Execute($dosql);

	}
	elseif (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7')
	{
		$dosql = "ALTER TABLE " . DB_PREFIX . "lb_sessions ALTER session_data TYPE text";
		$GLOBALS['lbdata']->Execute($dosql);
	}
	elseif (DB_TYPE == 'sqlite')
	{
		$dosql = "DROP TABLE " . DB_PREFIX . "lb_sessions";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "CREATE TABLE ".DB_PREFIX."lb_sessions (
				identifier VARCHAR(64) PRIMARY KEY,
				time BIGINT,
				session_data TEXT) )";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	// add jw_use_skin_colours to the players table
	$dosql = "INSERT INTO " . DB_PREFIX . "lb_players (name, value) VALUES (:name, :value)";
	$GLOBALS['lbdata']->prepareStatement ($dosql);
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'jw_use_skin_colours', ':value' => '1'));

	// add comment text editor to the settings table
	$dosql = "INSERT INTO " . DB_PREFIX . "lb_settings (name, value) VALUES (:name, :value)";
	$GLOBALS['lbdata']->prepareStatement ($dosql);
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'comment_text_editor', ':value' => '1'));

	// add amazon s3 settings
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'amazon', ':value' => '0'));
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'amazon_access', ':value' => ''));
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'amazon_secret', ':value' => ''));
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'amazon_bucket', ':value' => ''));				
	
	// remove static feeds setting
	$dosql = "DELETE FROM " . DB_PREFIX . "lb_settings WHERE name = 'staticfeed'";
	$GLOBALS['lbdata']->Execute($dosql);
?>
