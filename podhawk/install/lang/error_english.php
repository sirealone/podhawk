<?php

$e_message = array('cannot_connect' => 'I cannot connect to the ftp server. Please check that you have entered the correct address.',
		'cannot_login' => 'I have connected to the ftp server but I cannot log in - please check that you have entered the correct user name and password.',
		'cannot_find_upload' => "The ftp server cannot find the upload folder - you have probably entered an incorrect path. Use your normal ftp programme to check the path from your ftp 'root' to the upload folder.",
		'no_database' => 'I cannot find the database',
		'existing_database_1' => 'I have found tables in the database "',
		'existing_database_2' => '" which appear to be part of an existing PodHawk or LoudBlog installation. You need to delete or rename any tables with names which contain "lb_" before I can create a new PodHawk database. Or perhaps you wanted to update from LoudBlog to PodHawk. If so, run this installation programme again, but choose "Convert an existing LoudBlog installation to PodHawk" on the first page.',
		'db_problem' => 'Sorry - there is a problem with your database :<br />',
		'no_loudblog_config' => 'I cannot find your LoudBlog configuration file loudblog/custom/config.php.',
		'pre_lb_6' => 'earlier than LoudBlog 0.6',
		'lb8_only' => 'Please note : I can only convert the latest version (version 0.8) of LoudBlog to PodHawk. Your version is ',
		'lb8_only_2' => '. Please update to LoudBlog 0.8, and then convert to PodHawk.'
		);


?>
