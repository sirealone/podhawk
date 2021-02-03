<?php

$actiontype = array('backend');
include 'authority.php';

//do we want to update an existing posting?
if ((isset($_GET['do'])) AND ($_GET['do'] == "update"))
{
    $update = true;
    $update_id = $_GET['id'];
}
else
{
    $update_id = false;
    $update = false;
}

$smarty->assign(array(	'update'=> $update,
						'update_id' => $update_id));

//we need to know the upload limit
$smarty->assign('upload_limit', uploadlimit());

$smarty->assign (array(	'ftp' 			=> $reg->findSetting('ftp'),
						'ftp_details' 	=> $reg->ftpSettingsAvailable()));

//list everything in the upload directory...
$upload = get_dir_contents(PATH_TO_ROOT . '/upload');

//..except for index.html/index.php
foreach ($upload as $index=>$value)
{
	if ($value == 'index.html' || $value == 'index.php')
	{
		unset ($upload[$index]);
	}
}
$smarty->assign('upload', $upload);

//finally
if (!isset($message)) $message = "";
$smarty->assign('message', $message);
$smarty->assign('url_fopen', ini_get('allow_url_fopen'));
$smarty->assign('jwplayer_installed', jwplayer_installed());

$smarty->assign('record2_auth_key', $sess->createPageAuthenticator('record2'));
$smarty->assign('javaload_auth_key', $sess->createPageAuthenticator('javaload'));

$smarty->assign('flash_uploader', USE_FLASH_UPLOADER);

$smarty->assign('sessid', session_id());

?>
