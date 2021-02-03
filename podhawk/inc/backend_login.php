<?php

$actiontype = array('backend');
include 'authority.php';

$warning = false;

$accessLog = LO_EventLog::instance();

//choosing an appropriate welcome-message
if (!isset($_POST['login_name']))
{
	$addmessage = $trans_login['welcome'];
}
 
if (isset($_POST['login_name']))
{
	$addmessage = $trans_login['msg_wrongpassword'];
	$warning = true;
}

if (isset($accessError) && $accessError == true)
{
	$addmessage = "Sorry, there has been an error. Please log in again.";
	$warning = true;
}
	
if (isset($_GET['do']) && $_GET['do'] == "logout") 
{
	$addmessage = $trans_login['msg_logout'];
}

//We may have entered 'page=login' direct in the address bar; or we may have been sent here by an invalid $_GET.
// If so, kill the session and start a new one
if (isset($_SESSION['authorid']))
{
	$name = $reg->getNickname($_SESSION['authorid']);
	
	$accessLog -> write("$name logged out (possible invalid GET)");

	$sess->destroy();
	$sess->start();

}
    
$smarty->assign('add_message', $addmessage);
$smarty->assign('warning', $warning);
$smarty->assign('site_url', THIS_URL);
$smarty->assign('record1_auth_key', $sess->createPageAuthenticator('record1'));

$smarty->assign('challenge', $login->makeChallenge());
if (defined('USE_ENCRYPTED_HANDSHAKE'))
{
	$smarty->assign('use_encrypted_handshake', USE_ENCRYPTED_HANDSHAKE);
}
?>
