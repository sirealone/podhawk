<?php header("Content-type: application/rss+xml; charset=utf-8");

$actiontype = array('feed');
include 'authority.php';

$caching = $reg->findSetting('caching');

//check GETs 
cleanmygets();

//initialise smarty
$smarty = new SM_Feed();
$smarty->security = PODHAWK_SMARTY_SECURITY;
$smarty->security_settings = $podhawk_smarty_security_settings;
//for developing, we may want to clear caches
	//$smarty->clear_all_cache();
	//$smarty->clear_compiled_tpl();

	//clearing caches will remove the .htaccess files. Rewrite them.
	//$permissions = Permissions::instance();
	//$permissions->make_htaccess_all();

$smarty->caching = $caching;

$cache_id = "";
if ((count($_GET) == 0) && ($caching == true))
{
	$cache_id = "feed";
}

elseif ((isset($_GET['com'])) && ($_GET['com'] == "1"))
{
	$cache_id = 'feedcom';
}

elseif (isset($_GET['cat']))
{

	$catsdump = $reg->getCategoriesArray();
	foreach ($catsdump as $cat)
	{
		$catarray[] = str_replace(" ","",$cat['name']);
	}
	if (in_array(entity_encode($_GET['cat']),$catarray))
	{
		$cache_id = 'feed_'.$_GET['cat'];
	}
}

else
{
$smarty->caching = 0;
$caching = false;
}

if (($caching == true) && ($smarty->is_cached('feed.tpl',$cache_id)))
{
	$smarty->display('feed.tpl',$cache_id);
}
else
{
//header("Content-Type: text/plain");

	$dataSource = 'XM_FeedData'; // the class we will use to generate data for our feed

	$h = $plugins->event('onFeedDataReady'); // allows plugins to specify a different data source

	if ($h)
	{
		foreach ($h as $j) rewriteVariables($j);
	}

	$bool = (isset($_GET['com']) && $_GET['com'] == '1'); // do we want comments?

	$feed = new XM_RSSFeed($dataSource, $bool);

	$xml = $feed->build();

	$smarty->assign('xml', $xml);

	$smarty->display('feed.tpl', $cache_id);
		
}
?>
