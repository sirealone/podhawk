<?php

$actiontype = array('facebook_page', 'facebook_apptab');
include INCLUDE_FILES .'/authority.php';

// pagination

require "FacebookPagination.php";
require "FacebookData.php";

$pagination = new FacebookPagination();
$postings = array();

$rawPostings = $pagination->getRows();

$mp3_player_required = false;

if (count($rawPostings > 0))
{

	foreach ($rawPostings as $posting)
	{
		$postingManager = new FacebookData($posting);

		//$postingManager->setAppData($appData);

		$postingManager->extendPostingData();

		$key = $postingManager->getID();			

		$postings[$key] = $postingManager->getPosting();
	}	

	// do we need to display the mp3 player?	

	foreach ($postings as $posting)
	{
		if ($posting['playertype'] == 'flash')
		{
			$mp3_player_required = true;
		}
	}
}

// information about next, previous pages etc
$nextpageurl = $pagination->getNextPageURL();
$previouspageurl = $pagination->getPreviousPageURL();
$base_url = $pagination->getbaseURL();

// URL for adding a Page tab
$page_tab_url = "http://www.facebook.com/dialog/pagetab?app_id=$fb_id&amp;redirect_uri=$fb_canvas";

$smarty -> assign (array(	'settings' 				=> $reg->getSanitisedSettings(),
							'postingdata' 			=> $postings,
							'nextpage'				=> $pagination->getNextPage(),
							'nextpageurl' 			=> $nextpageurl,
							'previouspage'			=> $pagination->getPreviousPage(),
							'previouspageurl' 		=> $previouspageurl,
							'base_url' 				=> $base_url,
							'page_tab_url'			=> $page_tab_url,
							'single_post' 			=> isset($_GET['id']),
							'space' 				=> "&nbsp;&nbsp;",
							'feed' 					=> $reg->getFeedAddress(),
							'app_id' 				=> $fb_id,
							'mp3_player_required' 	=> $mp3_player_required,
							'jw_player_js_embed'	=> file_exists(PATH_TO_ROOT . '/podhawk/custom/players/jwplayer/jwplayer.js'),							
							'locale'				=> $plugins->getParam('facebook_plugin', 'locale')));

// finally, the Open Graph tags for the head section of the canvas page
if (ACTION == 'facebook_page')
{
	$fb = $plugins->plugins->facebook_plugin;
	$fb->makeOgTags($postings);

	$smarty->assign('og_tags', $fb->addMetaTags());
}
	
?>
