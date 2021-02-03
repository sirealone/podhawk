<?php

$actiontype = array('backend');
include 'authority.php';

echo "Pinging.....";

$plugins->event ('onPingPage');

?>
