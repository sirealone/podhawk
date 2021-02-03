<?php

$actiontype = array('backend');
include 'authority.php';

$feed = $reg->getFeedAddress();

if ($currentUser->isAdmin())
{
	$menu_array = array($trans_menu['p'] 		=> array(
				$trans_menu['record1'] 		=> 'index.php?page=record1',
				$trans_menu['playlist']		=> 'index.php?page=playlist',
				$trans_menu['postings']		=> 'index.php?page=postings',
				$trans_menu['find']			=> 'index.php?page=find'),

						$trans_menu['comments']	=> array(
				$trans_menu['comments']		=> 'index.php?page=comments',
				$trans_menu['spam']			=> 'index.php?page=spam'),

						$trans_menu['manage']	=> array(
				$trans_menu['settings']		=> 'index.php?page=settings',
				$trans_menu['authors1']		=> 'index.php?page=authors1',
				$trans_menu['cats']			=> 'index.php?page=cats',
				$trans_menu['images']		=> 'index.php?page=images',
				$trans_menu['players']		=> 'index.php?page=players',
				$trans_menu['plugins']		=> 'index.php?page=plugins',
				$trans_menu['utilities']	=> 'index.php?page=utilities'),

						$trans_menu['i']		=> array(
				$trans_menu['info']			=> 'index.php?page=info',
				$trans_menu['stats']		=> 'index.php?page=stats',
				$trans_menu['credits']		=> 'index.php?page=credits'),

						$trans_menu['h']		=> array(
				$trans_menu['forum']		=> 'http://www.podhawk.com/forum',
				$trans_menu['help']			=> 'http://www.podhawk.com/docs'),
				$trans_menu['upload']		=> array($trans_menu['av']			=> 'index.php?page=record1',
				$trans_menu['images']		=> 'index.php?page=images'),

						$trans_menu['y']		=> array(
				SITENAME					=> THIS_URL,
				$trans_menu['rss']			=> $feed)
					);
												
	if ($reg->findSetting('acceptcomments') == 'disqus')
	{
		$menu_array[$trans_menu['y']]['Disqus'] 		= 'http://disqus.com';
		$menu_array[$trans_menu['comments']]['Disqus'] 	= 'http://disqus.com';
	}

	if ($page == 'settings')
	{
		$menu_array[$trans_common['quick_links']] = array($trans_settings['sec_meta']	=> '#meta',
														$trans_settings['sec_webpage']	=> '#webpage',
														$trans_settings['sec_feed']		=> '#feed',
														$trans_settings['sec_comments']	=> '#comments',
														$trans_settings['sec_backend']	=> '#backend',
														$trans_settings['sec_filename']	=> '#filename',
														$trans_settings['sec_id3']		=> '#id3',
														$trans_settings['sec_ftp']		=> '#ftp');
	}
	
}
else
{
	$non_admin_menu = array($trans_common['nav'] => array(	$trans_menu['record1']	=> 'index.php?page=record1',
															$trans_menu['find']		=> 'index.php?page=find',
															$trans_menu['postings']	=> 'index.php?page=postings',
															$trans_menu['comments']	=> 'index.php?page=comments',
															$trans_menu['spam']		=> 'index.php?page=spam',
															$trans_menu['images']	=> 'index.php?page=images',
															$trans_menu['playlist']	=> 'index.php?page=playlist',
															$trans_menu['stats']	=> 'index.php?page=stats',
															$trans_menu['credits']	=> 'index.php?page=credits')
							);
}					

// plugins may need to add items to the menu
$h = $plugins->event("onCreateMenu");

	if ($h)
	{
		foreach ($h as $j) rewriteVariables($j);
	}	

if ($currentUser->isAdmin())
{
	$smarty->assign('menu', $menu_array);
}
else
{
	$smarty->assign('menu', $non_admin_menu);
}
																												
?>
