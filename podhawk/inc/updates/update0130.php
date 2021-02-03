<?php

if (DB_TYPE == 'mysql' || DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7')
	{
		$add = ($db['type'] == 'mysql') ? 'ADD' : 'ADD COLUMN';

		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ".$add." jw_streamer VARCHAR(64) DEFAULT NULL";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ".$add." jw_streaming_file VARCHAR(64) DEFAULT NULL";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ".$add." image VARCHAR(64) DEFAULT NULL";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ".$add." link VARCHAR(64) DEFAULT NULL";
		$GLOBALS['lbdata']->Execute($dosql);
	}	  

	if (DB_TYPE == 'sqlite')
	{
		include INCLUDE_FILES . '/updates/update_functions.php';

		sqlite_add_columns('postings', 'jw_streamer VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'jw-streaming_file VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'image VARCHAR(64)', "''");
		sqlite_add_columns('postings', 'link VARCHAR(64)', "''");
	}

// add the players table
	add_players_table();

//make changes to the settings table
	$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('disqus_name', '')";
	$GLOBALS['lbdata']->Execute($dosql);

	$dosql = "UPDATE ".DB_PREFIX."lb_settings SET value = 'none' WHERE name = 'acceptcomments'";
	$GLOBALS['lbdata']->Execute($dosql);

	$dosql = "DELETE FROM ".DB_PREFIX."lb_settings WHERE name = 'preventspam'";
	$GLOBALS['lbdata']->Execute($dosql);

	$dosql = "DELETE FROM ".DB_PREFIX."lb_settings WHERE name = 'use_akismet'";
	$GLOBALS['lbdata']->Execute($dosql);

//with postgres, we need to grant select privileges on the new players table to the front-end user
if (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7' || DB_FE_USER != "")
	{
		$dosql = "GRANT SELECT on ".DB_PREFIX."lb_players to ".$db['fe_user'].";";
		$GLOBALS['lbdata']->Execute($dosql);
	}

function add_players_table()
{
	$players = DB_PREFIX."lb_players";

	//players table  -- new in PodHawk 1.3
	$dosql = "CREATE TABLE ".$players." (
		name VARCHAR(32) PRIMARY KEY,
	  	value VARCHAR(255)
		)";
	$GLOBALS['lbdata']->Execute($dosql);

	$players_array = array(	'audio_player_type' 		=> 'loudblog',
							'emff_player' 				=> 'stuttgart',
							'emff_background' 			=> 'FFFFFF',
							'emff_standard_background'	=>'',
							'jw_audio_width' 			=> '280',
							'jw_audio_height' 			=> '20',
							'jw_backcolor' 				=> 'FFFFFF',
							'jw_controlbar' 			=> 'bottom',
							'jw_frontcolor' 			=> '000000',
							'jw_icons' 					=> 'true',
							'jw_lightcolor'				=> 'FFFFFF',
							'jw_logo' 					=> '',
							'jw_playlist' 				=> 'bottom',
							'jw_playlistsize'			=> '180',
							'jw_resizing' 				=> 'true',
							'jw_screencolor' 			=> '000000',
							'jw_skin' 					=> 'default',
							'jw_stretching' 			=> 'uniform',
							'jw_video_height' 			=> '225',
							'jw_video_width' 			=> '300',
							'pix_background' 			=> 'FFFFFF',
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
							'pix_width' 				=> '290'
				);
	foreach ($players_array as $name => $value)
	{
		$dosql = "INSERT INTO ".$players." (name, value) VALUES ('".$name."','".$value."')";
		$GLOBALS['lbdata']->Execute($dosql);
	}

}
?>
