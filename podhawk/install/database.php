<?php

$all_tables = array('access', 'authors', 'categories', 'comments', 'cookies', 'links', 'postings', 'settings', 'spam', 'players', 'plugins', 'sessions', 'visitors');

// SQL commands to create database tables - up to version 1.82

//first for mysql..
$tables_to_make = array("mysql" => array(

	"charset" => "ALTER DATABASE `".$_POST['sqldata']."` CHARACTER SET utf8;",

	"authors" => "CREATE TABLE ".DB_PREFIX."lb_authors (
  	id INTEGER PRIMARY KEY AUTO_INCREMENT,
 	nickname VARCHAR(32),
  	login_name VARCHAR(32),
  	password VARCHAR(32),
  	mail VARCHAR(128),
  	realname VARCHAR(64),
  	joined DATETIME,
  	edit_own INTEGER,
  	publish_own INTEGER,
  	edit_all INTEGER,
  	publish_all INTEGER,
  	admin INTEGER,
	hide INTEGER)",

	"categories" => "CREATE TABLE ".DB_PREFIX."lb_categories (
  	id INTEGER PRIMARY KEY AUTO_INCREMENT,
 	 name VARCHAR(32),
  	description VARCHAR(255),
	hide INT(2) )",

	"comments" => "CREATE TABLE ".DB_PREFIX."lb_comments (
  	id INTEGER PRIMARY KEY AUTO_INCREMENT,
  	posting_id INTEGER(11),
  	posted datetime,
  	name VARCHAR(64) ,
  	mail VARCHAR(128) ,
  	web VARCHAR(128) ,
  	ip VARCHAR(32) ,
  	user_agent VARCHAR(32), 
  	message_input text,
 	message_html text,
  	audio_file VARCHAR(255) ,
  	audio_type INTEGER(4) ,
  	audio_length INTEGER(8) ,
  	audio_size INTEGER(11) )",

	"cookies" => "CREATE TABLE ".DB_PREFIX."lb_cookies (
	identifier VARCHAR(32) PRIMARY KEY,
	id INTEGER(4),
	time BIGINT(20),
	user_agent VARCHAR(255) )",

	"links" => "CREATE TABLE ".DB_PREFIX."lb_links (
  	id INTEGER PRIMARY KEY AUTO_INCREMENT,
  	posting_id INTEGER(11) ,
  	description VARCHAR(255) ,
  	title VARCHAR(255) ,
  	url VARCHAR(255) ,
  	linkorder INTEGER(3) )",

	"players" => "CREATE TABLE ".DB_PREFIX."lb_players (
	name VARCHAR(32) PRIMARY KEY,
  	value VARCHAR(255) )",

	"plugins" => "CREATE TABLE " . DB_PREFIX . "lb_plugins (
	id INT(4) PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(64) DEFAULT NULL,
	full_name VARCHAR(64) DEFAULT NULL,
	enabled INT(3) DEFAULT '0',
	run_order INT(2) DEFAULT 3,
	params TEXT )",

	"postings" => "CREATE TABLE ".DB_PREFIX."lb_postings (
  	id INTEGER PRIMARY KEY AUTO_INCREMENT,
  	author_id INTEGER(4) ,
  	title VARCHAR(255) ,
  	posted DATETIME ,
  	filelocal INTEGER(2) ,
  	audio_file VARCHAR(255) ,
  	audio_type INTEGER(4) ,
  	audio_length INTEGER(8) ,
  	audio_size INTEGER(11) ,
  	message_input TEXT,
  	message_html TEXT,
  	comment_on INTEGER(2) ,
  	comment_size INTEGER(11) ,
  	category1_id INTEGER(4) ,
  	category2_id INTEGER(4) ,
  	category3_id INTEGER(4) ,
  	category4_id INTEGER(4) ,
  	tags TEXT , 
  	status INTEGER(2) ,
  	countweb INTEGER(11) ,
  	countfla INTEGER(11) ,
  	countpod INTEGER(11) ,
  	countall INTEGER(11) ,  
  	videowidth INTEGER(11) ,
  	videoheight INTEGER(11) ,
  	explicit INTEGER(2) ,
  	sticky INTEGER(2),
  	edited_with INTEGER(2) DEFAULT '0',
  	edit_date DATETIME,
  	jw_streamer VARCHAR(64),
  	jw_streaming_file VARCHAR(64),
  	image VARCHAR(64),
  	itunes_explicit INT(2) DEFAULT '0',
	summary TEXT,
	addfiles TEXT )",

	"sessions" => "CREATE TABLE ".DB_PREFIX."lb_sessions (
	identifier VARCHAR(64) PRIMARY KEY,
	time BIGINT(20),
	session_data TEXT )",

	"settings" => "CREATE TABLE ".DB_PREFIX."lb_settings (
  	name VARCHAR(32),
  	value VARCHAR(255) )",

	"spam" => "CREATE TABLE ".DB_PREFIX."lb_spam (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
  	permalink VARCHAR(255),
  	posting_id INTEGER(11),
  	posted datetime,
  	author VARCHAR(64) ,
  	email VARCHAR(128) ,
  	website VARCHAR(128) ,
  	user_ip VARCHAR(32) ,
  	user_agent VARCHAR(32),
  	body text,
  	message_html text )",

	"visitors" => "CREATE TABLE ".DB_PREFIX."lb_visitors (
	time BIGINT(20) ,
	ip VARCHAR(20) )"

		),

//...then for postgres...
	"postgres" => array(

	"authors" => "CREATE TABLE ".DB_PREFIX."lb_authors (
  	id SERIAL PRIMARY KEY,
  	nickname VARCHAR(32),
  	login_name VARCHAR(32),
  	password VARCHAR(32),
  	mail VARCHAR(128),
  	realname VARCHAR(64),
  	joined timestamp,
  	edit_own INTEGER,
  	publish_own INTEGER,
  	edit_all INTEGER,
  	publish_all INTEGER,
  	admin INTEGER,
	hide INTEGER )",

	"categories" => "CREATE TABLE ".DB_PREFIX."lb_categories (
  	id SERIAL PRIMARY KEY,
  	name VARCHAR(32),
  	description VARCHAR(255),
	hide INTEGER )",

	"comments" => "CREATE TABLE ".DB_PREFIX."lb_comments (
  	id SERIAL PRIMARY KEY,
  	posting_id INTEGER,
  	posted timestamp,
  	name VARCHAR(64) ,
  	mail VARCHAR(128) ,
  	web VARCHAR(128) ,
  	ip VARCHAR(32) ,
  	user_agent VARCHAR(32), 
  	message_input text,
  	message_html text,
  	audio_file VARCHAR(255) ,
  	audio_type INTEGER,
  	audio_length INTEGER,
  	audio_size INTEGER )",

	"cookies" => "CREATE TABLE ".DB_PREFIX."lb_cookies (
	identifier VARCHAR(32) PRIMARY KEY,
	id INTEGER,
	time BIGINT,
	user_agent VARCHAR(255) )",

	"links" => "CREATE TABLE ".DB_PREFIX."lb_links (
  	id SERIAL PRIMARY KEY,
  	posting_id INTEGER,
  	description VARCHAR(255),
  	title VARCHAR(255),
  	url VARCHAR(255),
  	linkorder INTEGER )",

	"players" => "CREATE TABLE ".DB_PREFIX."lb_players (
	name VARCHAR(32) PRIMARY KEY,
  	value VARCHAR(255) )",

	"plugins" => "CREATE TABLE " . DB_PREFIX . "lb_plugins(
	id SERIAL PRIMARY KEY,
	name VARCHAR(64) DEFAULT NULL,
	full_name VARCHAR(64) DEFAULT NULL,
	enabled INTEGER DEFAULT '0',
	run_order INTEGER DEFAULT 3,
	params TEXT )",

	"postings" => "CREATE TABLE ".DB_PREFIX."lb_postings (
  	id SERIAL PRIMARY KEY,
  	author_id INTEGER,
  	title VARCHAR(255) ,
  	posted timestamp,
  	filelocal INTEGER,
  	audio_file VARCHAR(255) ,
  	audio_type INTEGER,
  	audio_length INTEGER,
  	audio_size INTEGER,
  	message_input TEXT,
  	message_html TEXT,
  	comment_on INTEGER,
  	comment_size INTEGER,
  	category1_id INTEGER,
  	category2_id INTEGER,
  	category3_id INTEGER,
  	category4_id INTEGER,
  	tags TEXT , 
  	status INTEGER,
  	countweb INTEGER,
  	countfla INTEGER,
  	countpod INTEGER,
  	countall INTEGER,  
  	videowidth INTEGER,
  	videoheight INTEGER,
  	explicit INTEGER,
  	sticky INTEGER,
  	edited_with INTEGER DEFAULT '0',
  	edit_date timestamp,
  	jw_streamer VARCHAR(64),
  	jw_streaming_file VARCHAR(64),
  	image VARCHAR(64),
  	itunes_explicit INTEGER DEFAULT '0',
	summary TEXT,
	addfiles TEXT)",

	"sessions" => "CREATE TABLE ".DB_PREFIX."lb_sessions (
	identifier VARCHAR(64) PRIMARY KEY,
	time BIGINT,
	session_data TEXT )",

	"settings" => "CREATE TABLE ".DB_PREFIX."lb_settings (
  	name VARCHAR(32) PRIMARY KEY,
  	value TEXT )",

	"spam" => "CREATE TABLE ".DB_PREFIX."lb_spam (
	id SERIAL PRIMARY KEY,
  	permalink VARCHAR(255),
  	posting_id INTEGER,
  	posted timestamp,
  	author VARCHAR(64) ,
  	email VARCHAR(128) ,
  	website VARCHAR(128) ,
  	user_ip VARCHAR(32) ,
  	user_agent VARCHAR(32),
  	body text,
  	message_html text )",

	"visitors" => "CREATE TABLE ".DB_PREFIX."lb_visitors (
	time BIGINT PRIMARY KEY,
	ip VARCHAR(20) )"

		)	
	);

// ...while for sqlite, we delete 'AUTO_INCREMENT' from the mysql commands..
$tables_to_make['sqlite'] = str_replace(" AUTO_INCREMENT", "", $tables_to_make['mysql']);

//....and remove the charset command
unset ($tables_to_make['sqlite']['charset']);

$tables_to_make['sqlite3'] = $tables_to_make['sqlite'];

$insert_array = array ('authors' => 'INSERT INTO ' . DB_PREFIX . 'lb_authors (
								nickname,
								login_name,
								password,
								mail,
								joined,
								edit_own,
								publish_own,
								edit_all,
								publish_all,
								admin,
								hide ) VALUES (
								:nickname,
								:login_name,
								:password,
								:mail,
								:joined,
								:edit_own,
								:publish_own,
								:edit_all,
								:publish_all,
								:admin,
								:hide )',
						'categories' => 'INSERT INTO ' . DB_PREFIX . 'lb_categories (
								name,
								description,
								hide) VALUES (
								:name,
								:description,
								:hide)',
						'postings' => 'INSERT INTO ' . DB_PREFIX . 'lb_postings (
								author_id,
								title,
								posted,
								filelocal,
								audio_file,
								audio_type,
								audio_length,
								audio_size,
								message_input,
								message_html,
								comment_on,
								comment_size,
								status,
								countweb,
								countfla,
								countpod,
								countall,
								sticky ) VALUES (
								:author_id,
								:title,
								:posted,
								:filelocal,
								:audio_file,
								:audio_type,
								:audio_length,
								:audio_size,
								:message_input,
								:message_html,
								:comment_on,
								:comment_size,
								:status,
								:countweb,
								:countfla,
								:countpod,
								:countall,
								:sticky )'
						);
$insert_data_prepared_statement_array = array (
						'authors' => array(
								':nickname' => $_POST['nickname'],
								':login_name' => $_POST['login'],
								':password' => md5($_POST['password']),
								':mail' => $_POST['email'],
								':joined' => date("Y-m-d H:i:s"),
								':edit_own' => '1',
								':publish_own' => '1',
								':edit_all' => '1',
								':publish_all' => '1',
								':admin' => '1',
								':hide' => '0'),
						'categories' => array(
								':name' => 'default',
								':description' => 'this is the default category',
								':hide' => '0'),
						'postings' => array(
								':author_id' => '1',
								':title' => 'PodHawk',
								':posted' => date("Y-m-d H:i:s"),
								':filelocal' => '1',
								':audio_file' => 'trailer.mp3',
								':audio_type' => '1',
								':audio_length' => '7',
								':audio_size' => '28877',
								':message_input' => '',
								':message_html' => '',
								':comment_on' => '1',
								':comment_size' => '1048576',
								':status' => '3',
								':countweb' =>'0',
								':countfla' => '0',
								':countpod' => '0',
								':countall' => '0',
								':sticky' => '0' )
							);
								 

//insert name - value pairs into the database
$name_value_pairs = array("settings" => array("col1" => "name", "col2" => "value",
			"data" => array(
			'sitename' => 'My PodHawk',
			'slogan' => 'Podcasting to the world!',	
			'description' => 'My first PodHawk installation',
			'url' => $_POST['siteurl'],
			'markuphelp' => '1',
			'filename' => 'podcast',				
			'rename' => '1',
			'showlinks' => '10',				
			'id3_overwrite' => '0',
			'id3_album' => 'your podcast',
			'id3_artist' => 'your name',
			'id3_year' => '2009',
			'id3_genre' => 'Podcast',
			'id3_comment' => 'your comment',
			'rss_postings' => '10',
			'showpostings' => '15',
			'template' => 'default',				
			'ftp' => '0',
			'ftp_server' => '',
			'ftp_user' => '',
			'ftp_pass' => '',
			'ftp_path' => '',
			'itunes_author' => '',
			'itunes_email' => $_POST['email'],
			'itunes_explicit' => '0',
			'copyright' => '',
			'languagecode' => '0',
			'feedcat1' => '00-00',
			'feedcat2' => '00-00',
			'feedcat3' => '00-00',
			'feedcat4' => '00-00',
			'ping' => '0',
			'language' => 'english',
			'countweb' => '0',
			'countfla' => '0',
			'countpod' => '0',
			'acceptcomments' => 'none',
			'spamquestion' => 'Your favourite podcasting tool?',
			'spamanswer' => 'PodHawk',
			'previews' => '0',
			'ph_version' => THIS_PH_VERSION,
			'posts_per_page' => '5',
			'caching' => '1',
			'akismet_key' => '',
			'alternate_feed_address' => 'http://',
			'keep_spam' => '0',
			'count_visitors' => '0',
			'error_reporting' => '2',
			'preferred_date_format' => '%b %e, %Y',
			'template_language' => 'english',
			'disqus_name' => '',
			'homepage' => 0,
			'autosave' => 0,
			'comment_text_editor' => '1',
			'amazon' => '0',
			'amazon_access' => '',
			'amazon_secret' => '',
			'amazon_bucket' => ''
			) ), 

			"players" => array("col1"=>"name", "col2" => "value",
			"data" => array(
			'audio_player_type' => 'loudblog',
			'emff_player' => 'stuttgart',
			'emff_background' => 'FFFFFF',
			'emff_standard_background' =>'',
			'jw_audio_width' => '280',
			'jw_audio_height' => '20',
			'jw_backcolor' => 'FFFFFF',
			'jw_controlbar' => 'bottom',
			'jw_frontcolor' => '000000',
			'jw_icons' => 'true',
			'jw_lightcolor'=> 'FFFFFF',
			'jw_logo' => '',
			'jw_playlist' => 'bottom',
			'jw_playlistsize' => '180',
			'jw_resizing' => 'true',
			'jw_screencolor' => '000000',
			'jw_skin' => 'default',
			'jw_stretching' => 'uniform',
			'jw_video_height' => '225',
			'jw_video_width' => '300',
			'jw_use_skin_colours' => '1',
			'pix_background' => 'FFFFFF',
			'pix_border'=> '666666',
			'pix_height' => '24',
			'pix_leftbackground' => 'E4E5D4',
			'pix_lefticon' => '809AB1',
			'pix_loader' => 'E4E5D4',
			'pix_rightbackground' => '49647D',
			'pix_rightbackgroundhover' => '191970',
			'pix_righticon' => 'E4E5D4',
			'pix_righticonhover' => '536473',
			'pix_skip' => '666666',
			'pix_slider' => '191970',
			'pix_text' => '666666',
			'pix_track' => '6495ED',
			'pix_volslider' => '809AB1',
			'pix_voltrack' => 'E4E5D4',
			'pix_width' => '290') ) 

			);


$all_tables = array('authors', 'categories', 'comments', 'cookies', 'links', 'postings', 'settings', 'spam', 'players', 'plugins', 'sessions', 'visitors');

$drop_table = array('mysql'=>'DROP TABLE IF EXISTS ', 'postgres' => 'DROP TABLE ', 'sqlite' => 'DROP TABLE IF EXISTS ', 'sqlite3' => 'DROP TABLE IF EXISTS ');


?>
