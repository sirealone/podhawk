<?php

define ('ACTION', 'facebook_page');

// define constants and load basic stuff
require_once '../../../initialise.php';

// create a smarty object for the Facebook application
require "class.ph_facebook.php";

$smarty = new ph_facebook;
$smarty->security = PODHAWK_SMARTY_SECURITY;
$smarty->security_settings = $podhawk_smarty_security_settings;

//for developing, we may want to clear caches
//	$smarty->clear_all_cache();
//	$smarty->clear_compiled_tpl();

// check that the Facebook app is properly enabled
$enabled =  $plugins->getProperty('facebook_plugin', 'appPageEnabled');

if (!$enabled)
{
	die ("The Facebook application has not been fully installed for this PodHawk site");
}

// get instance of Facebook
require 'facebook.php';

$fb_app_name		= $plugins->getParam('facebook_plugin', 'app_name');
$fb_id 				= $plugins->getParam('facebook_plugin', 'app_id');
$fb_secret 			= $plugins->getParam('facebook_plugin', 'app_secret');
$fb_canvas 			= $plugins->getParam('facebook_plugin', 'canvas_page');
$fb_cancel 			= $fb_canvas . 'cancel.html';

$facebook = new Facebook(array('appId' => $fb_id, 'secret' => $fb_secret, 'cookie' => true));

$uid = $facebook->getUser();

// has facebook sent us a user id? If not, call the login/authorisation URL
if (empty($uid))
{
	$login_params = array('redirect_uri' => $fb_canvas);
	$login_url = $facebook->getLoginUrl($login_params);
	echo "<script> top.location.href='" . $login_url . "'</script>";
	exit;
}

// if we have a user id, use it to get the user's name
$url = 'https://graph.facebook.com/' . $uid;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			
$result = curl_exec($curl);
curl_close($curl);

if ($result)
{
	$result = json_decode($result);
	$user_name = $result->name;
}
else
{
	$user_name = "Guest";
}

// caching
$caching = $reg->findSetting('caching');
$smarty->caching = $caching;	
$cache_id = '';


if ($caching)
{	
	if (!isset($_GET['page'])) $_GET['page'] = 1;

	$cache_array = array('id', 'cat', 'tag', 'author', 'date', 'page', 'cal');

	//build the cache_id string
	foreach ($cache_array as $type)
	{
		if (isset($_GET[$type]))
		{
			$cache_id .= "|".$type."_".$_GET[$type];
		}
	}
	//remove leading |
	if ($cache_id != "")
	{
		$cache_id = substr($cache_id, 1);
	}


	//if we don't have a cache_id string, then all bets are off
	if ($cache_id == "")
	{
		$smarty->caching = 0;
		$caching = false;
	}
}

// create a dynamic (ie non-cached) block for the welcome message
$smarty->register_block('dynamic', 'smarty_block_dynamic', false);	
$smarty->assign ('name', $user_name);

// if we have a cached page, send it
if ($caching && $smarty->is_cached('index.tpl', $cache_id))
{
	$smarty->display('index.tpl', $cache_id);
}

// otherwise, get the necessary data and construct the page
else
{	
	require "getFacebookData.php";

	$smarty->display ('index.tpl', $cache_id);
}

?>
