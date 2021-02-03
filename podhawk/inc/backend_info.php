<?php

$actiontype = array('backend');
include 'authority.php';

if ($currentUser->isAdmin())
{
	phpinfo();
}
else
{
	echo ("Only Administrators can see this page");
}
?>
