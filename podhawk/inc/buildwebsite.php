<?php header("Content-type: text/html; charset=utf-8");

// ----------------------------------------------------- //
// PodHawk                                               //
// easy-to-use audioblogging and podcasting              //
//                           				// 
// http://www.podhawk.com                               //
//                                                       //
// Based on the fabulous 'LoudBlog'                      //
// by Gerrit van Aaken and Sebastian Stein               //
// Modified and extended by Peter Carter                 //
// Released under the Gnu General Public License         //
// http://www.gnu.org/copyleft/gpl.html                  //
//                                                       //
// Have Fun! Drop me a line if you like PodHawk!        //
// ----------------------------------------------------- //


	$actiontype = array('webpage');
	include 'authority.php';

	//clean GETs 
	$status = cleanmygets();

	if (!empty($status))
	{
		$log->write($status . ' IP address ' . $_SERVER['REMOTE_ADDR']);
	}

	// find a theme
	$theme 				= (isset($_GET['theme'])) ? $_GET['theme'] : $reg->findSetting('template');
	if (!is_dir(PATH_TO_ROOT . '/podhawk/custom/themes/' . $theme)) $theme = 'kubrick';	

	//things which need to be done before a cached template is called
	if ($reg->findSetting('count_visitors')) 
	{
		$visitor = US_Visitor::instance();
		
		$visitor->countVisitor();
	}

	//initialise smarty	
	$smarty = new SM_Webpage($theme);

	$smarty->security = PODHAWK_SMARTY_SECURITY;
	$smarty->security_settings = $podhawk_smarty_security_settings;	

	//for developing, we may want to clear caches
	//$smarty->clear_all_cache();
	//$smarty->clear_compiled_tpl();

	//clearing caches will remove the .htaccess files. Rewrite them.
	//$permissions = Permissions::instance();
	//$permissions->make_htaccess_all();

	$caching = $reg->findSetting('caching');
	// if we have comment data to display/process we must clear the cache and disable caching
	if (PO_Comment::commentDataToProcess() == true)
	{
		$smarty->clear_cache(null, 'id_'.$_GET['id']);

		$caching = false;
	}

	$smarty->caching = $caching;

	//we may need to make some parts of the template dynamic, even if cached
	$smarty->register_block('dynamic', 'smarty_block_dynamic', false);

	//we want to cache some pages
	$cache_id = "";
	
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
	}

	//if we don't have a cache_id string, then all bets are off
	if ($cache_id == "")
	{
		$smarty->caching = 0;
		$caching = false;
	}

	//do we want index.tpl or a different template?
	if (isset($_GET['atp']))
	{
		$tpl = $_GET['atp'].'.tpl';
	}
	else
	{
		$tpl = 'index.tpl';
	}

	//if the page is already cached, display it without further ado
	if ($caching && $smarty->is_cached($tpl, $cache_id))
	{
		//following line useful for debugging caching
		//$smarty->assign('cache_message', 'This page has been taken from cache');

		$smarty->display($tpl, $cache_id);
	}
	else
	{
		//else compute it
		require INCLUDE_FILES . '/getWebpageData.php';
		//following line useful for debugging caching
		//$smarty->assign('cache_message', 'This page has not been taken from cache');
		$smarty->display($tpl, $cache_id);
	}		

	// possible plugin actions after the page has been sent			
	$h = $plugins->event("onFinish");

?>
