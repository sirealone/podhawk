<?php

	//add plugins table to database
	if (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7')
	{
		$dosql = "CREATE TABLE " . DB_PREFIX . "lb_plugins (
		id SERIAL PRIMARY KEY,
		name VARCHAR(64) DEFAULT NULL,
		full_name VARCHAR(64) DEFAULT NULL,
		enabled INTEGER DEFAULT '0',
		run_order INTEGER DEFAULT 3,
		params VARCHAR(1024) DEFAULT NULL )";

	}
	else
	{

		$increm = (DB_TYPE == 'sqlite') ? "" : "AUTO_INCREMENT";
		
		$dosql = "CREATE TABLE " . DB_PREFIX . "lb_plugins (
		id INT(4) PRIMARY KEY " . $increm . ",
		name VARCHAR(64) DEFAULT NULL,
		full_name VARCHAR(64) DEFAULT NULL,
		enabled INT(3) DEFAULT '0',
		run_order INT(2) DEFAULT 3,
		params VARCHAR(1024) DEFAULT NULL )";
	
	}

	$GLOBALS['lbdata']->Execute($dosql);

	//remove settings which are now taken up by plugins, and the cgi setting (cgi uploading is now deprecated)
	$delete_settings = array('share_this', 'share_this_code', 'tweet', 'twitter_username', 'twitter_password', 'tweet_message', 'cgi');

	foreach ($delete_settings as $setting)
	{
		$dosql = "DELETE FROM ".DB_PREFIX."lb_settings WHERE name = " . escape($setting);
		$GLOBALS['lbdata']->Execute($dosql);
	}
?>
