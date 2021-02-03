<?php

$actiontype = array('backend');
include 'authority.php';

	if(!$authenticated) {
		$sess->write();
		die ('Hack attempt!');
	}

$ftpSettings['ftp_server'] 	= $reg->findSetting('ftp_server');
$ftpSettings['ftp_path'] 	= $reg->findSetting('ftp_path');
$ftpSettings['ftp_user'] 	= $reg->findSetting('ftp_user');
$ftpSettings['ftp_pass'] 	= $reg->findSetting('ftp_pass');

$smarty->assign('settings', $ftpSettings);
?>
