<?php

define('ACTION', "webpage");

$noValidURL = true;

require_once 'podhawk/initialise.php';

$requested = basename($_SERVER['REQUEST_URI']);

//make sure we have no GETs to mess things up
unset ($_GET);

// any plugins want to redirect the request for us?	
$h = $plugins->event('onRedirect', $requested);

if ($h)
{
	foreach ($h as $j) rewriteVariables($j);
}

if (!isset($_GET)) // if none of the plugins have set GETs, try the rewriter
{
	$rewriter = new Rewriter($requested);
	$rewriter->rewrite();
}

if (isset($_GET)) // has the rewriter or any of the plugins set any GETs?
{
	$noValidURL = false;
	require INCLUDE_FILES . "/buildwebsite.php";
}
else // page not found
{
	include (INCLUDE_FILES . '/notfound.php');
}

?>
