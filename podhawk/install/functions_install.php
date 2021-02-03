<?php

function check_existing_database($type)
{

	//we create an array of the tables in the database

	$tables = array();

	if ($type == 'mysql')
	{
		$dosql = "SHOW TABLES";
		$tables_array = $GLOBALS['lbdata']->GetArray($dosql);

		foreach ($tables_array as $subarray)
		{
			foreach ($subarray as $table)
			{
				$tables[] = strtolower($table);
			}
		}
	}

	elseif ($type == 'sqlite' || $type == 'sqlite3')
	{
		$dosql = "SELECT name FROM sqlite_master where type = 'table'";
		$thearray = $GLOBALS['lbdata']->GetArray($dosql);  
		foreach ($thearray as $tablearray)
		{
			 $tables[] = strtolower($tablearray['name']);
		}
	}

	elseif ($type == 'postgres8' || $type == 'postgres7')
	{
		$dosql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
		$tables_array = $GLOBALS['lbdata']->GetArray($dosql);
		foreach($tables_array as $table)
		{
			$tables[] = strtolower($table['table_name']);
		}
	}

	//if there are no existing tables in the database...
	if (empty($tables)) return false;

	// .. otherwise test whether any of the table names contain 'lb_'
	else
	{	
		foreach ($tables as $table)
		{		
			if (strpos($table, 'lb_') !== false) return true;			
		}

	return false;
	}
}	

//new backend user - called only when database is mysql and user is root
function create_backend_user ($user, $password, $data, $host) {

	$dosql = "GRANT ALL PRIVILEGES ON `".$data."`.* TO '".$user."'@'".$host."' IDENTIFIED BY '".$password."';";
	return $dosql;
	}


// new frontend user
function create_user ($type, $user, $password, $data, $host) {

	switch ($type) {

	case 'mysql' :
	
	$return = array(

		'create_user' => "CREATE USER '" . $user . "'@'" . $host . "' IDENTIFIED BY '" . $password . "'",

		"grant_select" => "GRANT SELECT on `" . $data . "`.* TO '" . $user . "'@'" . $host . "'",

		"grant_insert_comments" => "GRANT INSERT on `" . $data . "`.".DB_PREFIX."lb_comments TO '" . $user . "'@'" . $host . "'",

		"grant_visitors" => "GRANT INSERT, UPDATE, DELETE on `" . $data . "`.".DB_PREFIX."lb_visitors TO '" . $user . "'@'" . $host . "'",

		"grant_spam" => "GRANT INSERT, DELETE on `" . $data . "`.".DB_PREFIX."lb_spam TO '" . $user . "'@'" . $host . "'"  );
	
	break;

	case 'postgres' :

	$return = array(

		"create_user" => "CREATE ROLE ".$user." WITH LOGIN ENCRYPTED PASSWORD '".$password."';",

		"grant_select" => "GRANT SELECT on ".DB_PREFIX."lb_access, "
			.DB_PREFIX."lb_authors, "
			.DB_PREFIX."lb_categories, "
			.DB_PREFIX."lb_comments, "
			.DB_PREFIX."lb_links, "
			.DB_PREFIX."lb_postings, "	
			.DB_PREFIX."lb_settings, "
			.DB_PREFIX."lb_spam, "
			.DB_PREFIX."lb_players, "
			.DB_PREFIX."lb_plugins, "
			.DB_PREFIX."lb_visitors to ".$user.";",

		"grant_insert_comments" => "GRANT INSERT on ".DB_PREFIX."lb_comments to ".$user.";",

		"grant_visitors" => "GRANT INSERT, UPDATE, DELETE on ".DB_PREFIX."lb_visitors to ".$user.";",

		"grant_spam" => "GRANT INSERT, DELETE on ".DB_PREFIX."lb_spam to ".$user.";",

		"grant_comments_seq" => "GRANT UPDATE on ".DB_PREFIX."lb_comments_id_seq to ".$user.";",

		"grant_spam_seq" => "GRANT UPDATE on ".DB_PREFIX."lb_spam_id_seq to ".$user.";");

	break;

	case 'sqlite' :
	case 'sqlite3' :

	$return = array();

	break;

	} // end switch

	return $return;

}	

function convert_database($type)
// converts a LoudBlog database to PodHawk
{

	$success = true;
	$error_message = "<p class=\"msg\">Sorry - there is a problem in writing to your database:<br />";

	//SQLite does not do auto_increment!! and sqlite2 does not support "if not exists"
	if ($type == "sqlite")
	{
    	$increm = "";
		$not_exists = "";
		$exists = "";
	}
	else
	{
    	$increm = "AUTO_INCREMENT";
		$not_exists = "IF NOT EXISTS ";
		$exists = "IF EXISTS ";
	}

	//spam table

	$dosql_array[] = "CREATE TABLE ".$not_exists.DB_PREFIX."lb_spam (
	id INTEGER PRIMARY KEY ".$increm.",
  	permalink VARCHAR(255),
  	posting_id INTEGER(11),
 	posted datetime,
  	author VARCHAR(64) ,
  	email VARCHAR(128) ,
  	website VARCHAR(128) ,
	user_ip VARCHAR(32) ,
	user_agent VARCHAR(32),
	body text,
	message_html text
	)";

	//Visitors table 
	$dosql_array[] = "CREATE TABLE ".$not_exists.DB_PREFIX."lb_visitors (
		time BIGINT(20) ,
		ip VARCHAR(20) )";

	//Cookies table - new in PodHawk 1.5
	$dosql_array[] = "CREATE TABLE ".$not_exists.DB_PREFIX."lb_cookies (
		identifier VARCHAR(32) PRIMARY KEY,
		id INTEGER(4),
		time BIGINT(20),
		user_agent VARCHAR(255) )";

	//Sessions table - new in PodHawk 1.5
	$dosql_array[] = "CREATE TABLE ".$not_exists.DB_PREFIX."lb_sessions (
		identifier VARCHAR(64) PRIMARY KEY,
		time BIGINT(20),
		session_data VARCHAR(256) )";

	//Plugins table - new in PodHawk 1.7
	$dosql_array[] = "CREATE TABLE " . $not_exists . DB_PREFIX . "lb_plugins(
		id INT(4) PRIMARY KEY " . $increm . ",
		name VARCHAR(64) DEFAULT NULL,
		full_name VARCHAR(64) DEFAULT NULL,
		enabled INT(3) DEFAULT '0',
		run_order INT(2) DEFAULT 3,
		params VARCHAR(256) DEFAULT NULL )";

	//players table  -- new in PodHawk 1.3
	$dosql_array[] = "CREATE TABLE ".$not_exists.DB_PREFIX."lb_players (
		name VARCHAR(32) PRIMARY KEY,
	  	value VARCHAR(255) )";

	foreach ($dosql_array as $dosql)
	{

		try
		{
			$result = $GLOBALS['lbdata']->Execute($dosql);
		}

		catch (exception $e)
		{
			$success = false;
			$error_message .= $e->getMessage() . "<br />";
		}
	}

	$players_array = array( 'audio_player_type' 		=> 'loudblog',
							'emff_player' 				=> 'stuttgart',
							'emff_background' 			=> 'FFFFFF',
							'emff_standard_background' 	=>'',
							'jw_audio_width' 			=> '280',
							'jw_audio_height' 			=> '20',
							'jw_backcolor' 				=> 'FFFFFF',
							'jw_controlbar' 			=> 'bottom',
							'jw_frontcolor' 			=> '000000',
							'jw_icons' 					=> 'true',
							'jw_lightcolor'				=> 'FFFFFF',
							'jw_logo' 					=> '',
							'jw_playlist' 				=> 'bottom',
							'jw_playlistsize' 			=> '180',
							'jw_resizing' 				=> 'true',
							'jw_screencolor' 			=> '000000',
							'jw_skin' 					=> 'default',
							'jw_stretching' 			=> 'uniform',
							'jw_video_height' 			=> '225',
							'jw_video_width'			=> '300',
							'pix_background'			=> 'FFFFFF',
							'pix_border'				=> '666666',
							'pix_height' 				=> '24',
							'pix_leftbackground' 		=> 'E4E5D4',
							'pix_lefticon' 				=> '809AB1',
							'pix_loader' 				=> 'E4E5D4',
							'pix_rightbackground' 		=> '49647D',
							'pix_rightbackgroundhover' 	=> '191970',
							'pix_righticon' 			=> 'E4E5D4',
							'pix_righticonhover' 		=> '536473',
							'pix_skip' 					=> '666666',
							'pix_slider' 				=> '191970',
							'pix_text' 					=> '666666',
							'pix_track' 				=> '6495ED',
							'pix_volslider' 			=> '809AB1',
							'pix_voltrack' 				=> 'E4E5D4',
							'pix_width'					=> '290'
				);

	try
	{
		$dosql = "INSERT INTO ".DB_PREFIX."lb_players (name, value) VALUES (:name, :value)";
		$GLOBALS['lbdata'] -> prepareStatement($dosql);

		foreach ($players_array as $name => $value)
		{
			$insertArray = array(':name' => $name, ':value' => $value);
			$GLOBALS['lbdata'] -> executePreparedStatement($insertArray);
		}
	}	
	catch (exception $e)
	{
		$success = false;
		$error_message .= $e->getMessage() . "<br />";
	}
	
	// add columns to authors, comments and postings tables
	if ($type == "sqlite")
	{
		sqlite_add_columns('authors',"login_name VARCHAR(32)","''");
		sqlite_add_columns('comments', 'user_agent VARCHAR(32)', "''");
		sqlite_add_columns('postings', 'edited_with','0');
		sqlite_add_columns('postings', 'edit_date', "''");
		sqlite_add_columns('postings', 'jw_streamer VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'jw_streaming_file VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'image VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'link VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'itunes_explicit INT(2)', "0");

	}
	else //mysql
	{ 

		$dosql_array = array(
				"ALTER TABLE ".DB_PREFIX."lb_authors ADD login_name VARCHAR(32) DEFAULT NULL AFTER nickname",
				"ALTER TABLE ".DB_PREFIX."lb_comments ADD user_agent VARCHAR(32) DEFAULT NULL",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD edited_with INT(2) DEFAULT '0'",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD edit_date DATETIME DEFAULT NULL",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD jw_streamer VARCHAR(64) DEFAULT NULL",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD jw_streaming_file VARCHAR(64) DEFAULT NULL",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD image VARCHAR(64) DEFAULT NULL",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD link VARCHAR(64) DEFAULT NULL",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD itunes_explicit INT(2) DEFAULT '0'",
				"ALTER TABLE ".DB_PREFIX."lb_postings ADD summary TEXT DEFAULT NULL"			
				);

		foreach ($dosql_array as $dosql)
		{

			try
			{
				$result = $GLOBALS['lbdata']->Execute($dosql);
			}

			catch (exception $e)
			{
				$success = false;
				$error_message .= $e->getMessage() . "<br />";
			}
		}
	}
			
	
	$dosql = "SELECT * FROM " . DB_PREFIX . "lb_settings";
	$settings = $GLOBALS['lbdata']->GetAssoc($dosql);

	//add new settings
	$settings_array = array('ph_version' 				=> 1.73, // changes post Pod|Hawk 1.73 are made by the autoupdate script 
							'posts_per_page' 			=> '5',  
							'caching' 					=> '1',  
							'akismet_key' 				=> '',  
							'alternate_feed_address' 	=> 'http://',
							'keep_spam' 				=> '0',  
							'count_visitors' 			=> '0',
							'error_reporting' 			=> '2',
							'preferred_date_format' 	=> '%b %e, %Y',
							'template_language' 		=> 'english',
							'disqus_name' 				=> '',
							'homepage' 					=> 0, 
							'autosave'					=> 0,
							'comment_text_editor' 		=> '1'); 

	try
	{
		$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES (:name, :value)";
		$GLOBALS['lbdata'] -> prepareStatement($dosql);

		foreach ($settings_array as $name => $value)
		{
			if (!isset($settings[$name]))
			{
				$insertArray = array(':name' => $name, ':value' => $value);
				$GLOBALS['lbdata'] -> executePreparedStatement($insertArray);
			}
		}
	}		
	catch (exception $e)
	{
		$success = false;
		$error_message .= $e->getMessage() . "<br />";
	}			
		
	$dosql_array = array(
			"UPDATE ".DB_PREFIX."lb_authors SET login_name = nickname",
			"UPDATE ".DB_PREFIX."lb_settings SET value = 'english' WHERE name = 'language'",
			"UPDATE ".DB_PREFIX."lb_settings SET value = 'none' WHERE name = 'acceptcomments'"
				);

	foreach ($dosql_array as $dosql)
	{
	
		try
		{
			$result = $GLOBALS['lbdata']->Execute($dosql);
		}

		catch (exception $e)
		{
			$success = false;
			$error_message .= $e->getMessage() . "<br />";
		}
	}	

	//delete unneeded settings
	$delete_array = array( 'flashcom_on',
							'dateformat',
							'comments_on',
							'staticfeeds_tags',
							'badbehavior',
							'badbehaviour',
							'version080',
							'emergency_email',
							'version06',
							'version07',
							'version071',
							'bb2_display_stats',
							'bb2_verbose',
							'bb2_strict',
							'bb2_installed',
							'imagedir',
							'preventspam',
							'cgi');

	foreach ($delete_array as $name)
	{
		if (isset($settings[$name]))
		{
			$dosql = "DELETE FROM ".DB_PREFIX."lb_settings WHERE name = '".$name."'";
			$GLOBALS['lbdata']->Execute($dosql);
		}
	}
	//and drop the bad behavior table if it exists
	$dosql = "DROP TABLE ".$exists."bad_behavior";
	$GLOBALS['lbdata']->Execute($dosql);

	$return = array($success, $error_message);
	return $return;

}

function sqlite_add_columns($table, $new_columns, $new_data)
{

	//ADOdb supports sqlite2, not sqlite3, and sqlite2
	//requires this convoluted procedure to add a column to a table

	//if we try to add new columns which are already in the table, we risk losing the table completely
	if (sqlite_column_test($table, $new_columns))
	{
		return;
	}
	else
	{

		$table = DB_PREFIX."lb_".$table;

		//get the definition of $table
		$dosql = "SELECT * FROM sqlite_master WHERE tbl_name = '".$table."' AND type = 'table'";
		$result = $GLOBALS['lbdata']->GetArray($dosql);

		//create a temporary table and transfer everything in $table to it
		$dosql = "CREATE TABLE temp AS SELECT * FROM ".$table;
		$GLOBALS['lbdata']->Execute($dosql);

		//destroy $table
		$dosql = "DROP TABLE ".$table;
		$GLOBALS['lbdata']->Execute($dosql);

		//make a new $table with the new columns added at the end
		$create_table = substr($result[0]['sql'],0,-1).",".$new_columns.")";

		//if the new table is successfully created...
		if ($GLOBALS['lbdata']->Execute($create_table))
		{
			//transfer data from temp, adding data for the new columns
			$dosql = "INSERT INTO ".$table." SELECT *,".$new_data." FROM temp";
			$GLOBALS['lbdata']->Execute($dosql);
		}
		else
		{
			//..else restore the old $table from temp
			$dosql = "CREATE TABLE ".$table." AS SELECT * FROM temp";
			$GLOBALS['lbdata']->Execute($dosql);
		}

		//destroy temp - its work is finished
		$dosql = "DROP TABLE temp;";
		$GLOBALS['lbdata']->Execute($dosql);
	}
}	

function sqlite_column_test ($table, $column)
{
	$table = DB_PREFIX."lb_".$table;
	$dosql = "SELECT * FROM sqlite_master WHERE type = 'table' and name = '" . $table . "'";
	$result = $GLOBALS['lbdata']->GetArray($dosql);
	$sqlstring = $result[0]['sql'];
	$result = strpos($sqlstring, $scolumn);
	return $result;
}

function sql_column_test ($table, $column)
{
	$table = DB_PREFIX."lb_".$table;
	$dosql = "SHOW COLUMNS FROM ".$table." LIKE '" . $column ."'";
	$columns = $GLOBALS['lbdata']->Execute($dosql);
	$count = $columns->RecordCount();
	$result = ($count > 0);
	return $result;
}

function move_images()
{

	$audiopath = PATH_TO_ROOT."/audio/";
	$imagespath = PATH_TO_ROOT."/images/";
	$images_array = array('itunescover', 'rssimage', 'temp_image');
	$image_types = array ('gif', 'png', 'jpg', 'jpeg');

	foreach ($images_array as $image)
	{
		foreach($image_types as $type)
		{
			$file = $image.".".$type;

			if (file_exists($audiopath.$file))
			{
				rename($audiopath.$file, $imagespath.$file);
			}
		}
	}
}
		
?>
