<?php

##########################################################################
// USER CONFIGURATION
// enter here the maximum age (days) of the image files you want to find
// or 0 to find all image files

$days = 0;
##########################################################################

$seconds = $days * 24 * 60 * 60;

$output = '';

$output .= 'var tinyMCEImageList = new Array(';

if ($authenticated)
{

	$direc = opendir(IMAGES_PATH);

	$theImages = array();

	while ($file = readdir($direc))
	{
		if (strpos('~', $file) === false && substr($file, 0, 1) != '.') // no back-up or hidden files
		{            
			if (is_file(IMAGES_PATH . $file) && getimagesize(IMAGES_PATH . $file) != FALSE)  // if the file is an image file
			{
				// check whether the file is more than $days days old
				$now = time();
				$current = true;
				if ($days)
				{
					$current = ((filemtime(IMAGES_PATH . $file) + $seconds) > $now);
				}

				// add to images array
				if ($current)
				{ 
					$theImages[] = $file;
				}      
			
			}
		}
	}

	// sort the list of image files alphabetically
	natcasesort($theImages);

	// turn the array of image files into elements of a javascript array
	foreach ($theImages as $file)
	{
		$output .= "\n"
			. '["'
			. utf8_encode($file)
			. '", "'
			. utf8_encode(THIS_URL . "/images/$file")
			. '"],';
	}

	if (!empty($theImages))
	{
		$output = substr($output, 0, -1); // remove last comma from array item list (breaks some browsers)
		$output .= "\n";
	}

	closedir($direc);

}// close "if $authenticated"

// close the javascript array
$output .= ');';

// headers
header ('Content-type: text/javascript'); // overwrites the text/html header at top of buildbackend.php
header ('pragma: no-cache');
header ('expires: Wed, 18 April 2012 00:00:00 GMT'); // set 'expires' date in past to prevent caching

// send the javascript array to the browser
echo $output;

?>
