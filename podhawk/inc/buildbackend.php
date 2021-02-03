<?php header("Content-type: text/html; charset=utf-8");

// ----------------------------------------------------- //
// PodHawk                                               //
// easy-to-use audioblogging and podcasting              //
// Version 1.7 (September 2010)                           // 
// http://www.podhawk.com                               //
//                                                       //
// Based on the fabulous 'LoudBlog'                      //
// by Gerrit van Aaken and Sebastian Stein               //
// Modified and extended by Peter Carter                 //
// Released under the Gnu General Public License         //
// http://www.gnu.org/copyleft/gpl.html                  //
//                                                       //
// Have Fun! Drop me a line if you like Podhawk!        //
// ----------------------------------------------------- //


	$actiontype = array('backend');
	include 'authority.php';

	$plugin = "";
	$page = "";
	$pages_from_plugins = array();
	$page_data_from_plugins = array();
	$plugin_trans_array = array();
	$plugins_trans_menu = array();

	$clear = DA_CacheClear::instance();

	// some preliminary stuff	

	//get the language translation table
	if (isset($_GET['language']) && in_array($_GET['language'], whitelist('backend_languages')))
	{	
		$langToUse = $_GET['language'];
	}
	
	else
	{
		$langToUse = $reg->findSetting('language');			
	}

	include_once (PATH_TO_ROOT."/podhawk/lang/$langToUse.php");

	
	// now down to business - first check login status

	require INCLUDE_FILES . "/accesscheck.php";

	//show the login-screen if access is denied
	if (!$access)
	{	
		$_GET['page'] = "login";

	}
	else
	{
	 	//check if we need to update the database
		$autoupdate = new Autoupdate(PH_VERSION);

		$autoupdate->update();

		$message = $autoupdate->getMessage();
	}

	//default page is 'postings'
	if (!isset($_GET['page']))
	{
			$_GET['page'] = "postings";
	}

	// check whether any plugins have their own backend pages
	 
	$p = $plugins->event("registerBackendPages");

	//create two arrays from data returned from plugins
	foreach ($p as $q)
	{
		$page_name = $q['page_name'];
		$pages_from_plugins[] = $page_name; // an array of page names from plugins
		$page_data_from_plugins[$page_name] = $q; //an array of the data returned from plugins, with name of page as the key
	}

	//sanity/security check $_GET['page'] against a whitelist of known pages
	$whitelist = array_merge(whitelist('backend_pages'), $pages_from_plugins);
	 
	//if $_GET['page'] is not in whitlist or $_GET['id'] is not a number, go to login
	$page = (in_array($_GET['page'], $whitelist)) ? $_GET['page'] : 'login';

	if (isset($_GET['id']) && !ctype_digit($_GET['id']))
	{
		$page = 'login';
	}

	//then get the additional translation array for menu items added by plugins
	//$plugins_trans_menu = $plugins->getMenuTrans('deutsch');

	// check if the page has a valid authentication code
	$authenticated = $sess->authenticate();

	// create Smarty instance - first if the requested page is a plugin page
	if (in_array($page, $pages_from_plugins))
	{
		$plugin = $page_data_from_plugins[$page]['plugin'];
		$smarty = new SM_Plugin($plugin);		
	}
	else
	{ //..or if the page is a normal backend page

		$smarty = new SM_BackendPage();
	}
	
	//for developing, we may want to clear caches
	//$smarty->clear_all_cache();
	//$smarty->clear_compiled_tpl();

	// set Smarty security
	$smarty->security = PODHAWK_SMARTY_SECURITY;
	$smarty->security_settings = $podhawk_smarty_security_settings;		
	
	// find if the logged in user has admin privileges and tell Smarty
	if ($page != 'login')
	{
		$smarty->assign('admin', $currentUser->isAdmin());
		include INCLUDE_FILES . '/menu.php';
	}

	// assign some important variables to Smarty
	$smarty->assign('version', PH_VERSION);
	$smarty->assign('page', $page);
	$smarty->assign('url', THIS_URL);
	$smarty->assign('sitename', SITENAME);
	$smarty->assign('jquery_location', JQUERY_LOCATION);

	//define the page template
	$template = 'manager_'.$page.'.tpl';

	//locate the backend page script, the translation file and the page template
	if (!empty($plugin))
	{
		require PLUGINS_DIR . $plugin . "/" . "backend_$page.php";
		$smarty->assign('trans', $plugins->plugins->$plugin->getTranslationArray());
		$path_to_template = PLUGINS_DIR . $plugin . "/" . $template;
	}
	else
	{ 
		require INCLUDE_FILES . "/backend_".$page.".php";

		$t = new TR_TranslationBackend($page);

		$smarty->assign('trans', $t->getTrans());

		$path_to_template = PATH_TO_ROOT . "/podhawk/smarty/templates/" . $template;
	}

	//assign to Smarty possible information from plugins
	$smarty->assign('plugins_css', $plugins->event("addCSS"));
	$smarty->assign('plugins_head_script', $plugins->event("addHeadScript"));
	$smarty->assign('plugins_body_script', $plugins->event("addBodyScript"));

	
	if (file_exists($path_to_template))
	{
		$smarty->display($template);
	}

	//record any non-authenticated attempts to write to the database
	
	if (isset($_GET['do']) && $_GET['do'] != 'logout' && $_GET['do'] != 'allposts' && @!$authenticated)
	{
		$log->write('Non-authenticated attempt to write to database');
	}
	// possible plugin actions after the page has been sent
	$h = $plugins->event("onFinish");

	// if any 'clear caches' flags have been set, action them now
	$clear->clearCaches();

	//if we are storing session data in the database, we must write the data now, before the database object is destroyed
	$sess->write();

?>
