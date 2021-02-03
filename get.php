<?php
// test to see that new subversion repository works properly
define ('ACTION', 'find_audio');

require_once 'podhawk/initialise.php';

try
{
	if (count($_GET) != 1) //there must be one but only one GET
	{
		throw new Exception("Multiple GET values in request for an audio file.");
	}

	$value = reset($_GET);
	$key = key($_GET);
	
	if ($key == 'com')
	{
		session_start();
		$audio = new PO_Find_Audio_Comment;
		$audio->get();
	}
	
	elseif (substr($value,0,1) == 'j')
	{
		$audio = new PO_Count_Download($_GET);
		$audio->get();
	}
	else
	{
		throw new Exception("Unrecognised request received in get.php.");
	}
}	
catch (Exception $e)
{
	$log->write($e->getMessage());
	include (INCLUDE_FILES . '/notfound.php');
}
?>
