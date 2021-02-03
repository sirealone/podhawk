<?php

	define ('ACTION', 'backend');

	require "../../../initialise.php";

	$permissions->make_writable('images');

	$imageSource = $_GET['url'];
	$imageName = basename($imageSource);
	$imageDest = IMAGES_PATH . 'temp_' . $imageName;

	
	$ch = curl_init($imageSource);
	$fp = fopen($imageDest, "wb");

	$options = array(	CURLOPT_FILE => $fp,
						CURLOPT_HEADER => 0,
						CURLOPT_FOLLOWLOCATION => 1,
						CURLOPT_TIMEOUT => 60); // 1 minute timeout (should be enough) 

	curl_setopt_array($ch, $options);
	curl_exec($ch);
	curl_close($ch);

	fclose($fp);

	$permissions->make_not_writable('images');
	
	// get image data from downloaded image
	// get mime type and image size
	// headers

	readfile($imageDest);

	//unset ($imageDest);



?>
