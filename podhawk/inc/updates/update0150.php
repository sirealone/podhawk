<?php

	//Cookies table - new in PodHawk 1.5
	$dosql = "CREATE TABLE ".DB_PREFIX."lb_cookies (
	identifier VARCHAR(32),
	id INTEGER(4),
	time BIGINT(20),
	user_agent VARCHAR(255) )";

	$GLOBALS['lbdata']->Execute($dosql);

	//Sessions table - new in PodHawk 1.5

	$dosql = "CREATE TABLE ".DB_PREFIX."lb_sessions (
	identifier VARCHAR(64),
	time BIGINT(20),
	session_data VARCHAR(512) )";

	$GLOBALS['lbdata']->Execute($dosql);


	$settings_array = array('homepage' 			=> 0,
							'share_this' 		=> 0,
							'share_this_code' 	=> '',
							'tweet' 			=> 0,
							'twitter_username' 	=> '',
							'twitter_password' 	=> '',
							'tweet_message' 	=> 'New posting ||title|| on ||sitename||. See it at ||url||?id=||id||');

	foreach ($settings_array as $name=>$value)
	{
		$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('".$name."', '".$value."');";
		$GLOBALS['lbdata']->Execute($dosql);
		
	}
?>
