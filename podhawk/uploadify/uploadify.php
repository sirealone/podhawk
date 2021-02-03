<?php
/*
Uploadify v2.1.0
Release Date: August 24, 2009

Copyright (c) 2009 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

define ('ACTION', 'backend');

require ("../initialise.php");

if (!uploadify_auth()) die ();

if (!empty($_FILES))
{
	if ($_GET['upload_type'] == 'audio')
	{
		$writable = 'upload';
		$targetPath = UPLOAD_PATH;
	}
	else
	{
		$writable = 'images';
		$targetPath = IMAGES_PATH;
	}
	

	$permissions->make_writable($writable);

	$tempFile = $_FILES['Filedata']['tmp_name'];
	
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	
	move_uploaded_file($tempFile,$targetFile);

	echo "1";

	$permissions->make_not_writable($writable);
	
}

function uploadify_auth()
{

	if (!isset($_GET['auth']))
	{
		return false;
	}

	if (STORE_SESSIONS_IN_DATABASE == true)
	{

		$life = (defined('SESSION_LIFE')) ? SESSION_LIFE : 1200;
		$timeout = time() - $life;

		$dosql = "DELETE FROM ".DB_PREFIX."lb_sessions WHERE time < ".$timeout.";";
		$GLOBALS['lbdata']->Execute($dosql);

		$identifier = $_GET['auth'];
		$dosql = "SELECT session_data FROM ".DB_PREFIX."lb_sessions WHERE identifier = ".escape($identifier).";";
		$result = $GLOBALS['lbdata']->GetArray($dosql);

		if (empty($result[0]['session_data']))
		{			
			return false;
		}
		else
		{
			return true;
		}

	}
	else
	{
		$session_id ($_GET['auth']);
		$session_start();

		if (!isset($_SESSION['authorid']))
		{
			return false;
		}
		else
		{
			return true;
		}

	}

}

?>
