<?php

$actiontype = array('backend');
include 'authority.php';

$message = "";
$warning = false;
$skinsDir = PATH_TO_ROOT . '/podhawk/custom/players/jwplayer/skins/';

try
{
	if($currentUser->isAdmin() == false)
	{
		throw new Exception('adminonly');
	}


	if (isset($_GET['do']))
	{
		try
		{
			if (!$authenticated)
			{
				throw new Exception('no_auth');
			}
		
			if ($_GET['do'] == 'save')
			{
				// put $_POST data into database
				if (!isset($_POST['emff_standard_background']))
				{
					$_POST['emff_standard_background'] = false;
				}

				$dosql = 'UPDATE ' . DB_PREFIX . 'lb_players SET value = :value WHERE name = :name';
				$GLOBALS['lbdata'] -> prepareStatement($dosql);

				foreach ($_POST as $name => $value)
				{
					$GLOBALS['lbdata']->executePreparedStatement(array(':value' => $value, ':name' => $name));			
				}
				$clear->setFlag(array('SmartyCache'));
			}
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$warning = true;
		}	
	}

	// get data from the players table and assign it to smarty

	$emff_contents = get_dir_contents('custom/players/emff');
	sort($emff_contents);
	foreach ($emff_contents as $content)
	{
		if ($content == "emff_debug.swf") continue;
		$emff_players[] = substr($content,5,-4);
	}

	
	$skins_contents = get_dir_contents($skinsDir);
	$skins = array();

	foreach ($skins_contents as $skin)
	{
		if (substr($skin, -3) == "swf" || (is_dir ($skinsDir . $skin) && is_readable($skinsDir . $skin . '/' . $skin . '.zip')))
		{
			$skins[] = $skin;
		}
	}

	$players = $reg->refreshPlayers();

	$pixelout_param_names = array('pix_background','pix_leftbackground','pix_lefticon','pix_rightbackground',
						'pix_rightbackgroundhover','pix_righticon', 'pix_righticonhover','pix_text','pix_slider',
						'pix_track','pix_border','pix_loader','pix_voltrack','pix_volslider','pix_skip');

	$smarty->assign('players', $players);
	$smarty->assign('emff_players', $emff_players);
	$smarty->assign('jw_skins', $skins);
	$smarty->assign('pixelout_param_names',$pixelout_param_names);
	$smarty->assign('jw_player_installed', jwplayer_installed());
	$smarty->assign('theme', $reg->findSetting('template'));

	$smarty->assign('players_auth_key', $sess->createPageAuthenticator('players'));
}
catch (Exception $e)
{
	$message = $e->getMessage();
	$warning = true;
}

$smarty->assign('message',$message);
$smarty->assign('warning',$warning);

?>
