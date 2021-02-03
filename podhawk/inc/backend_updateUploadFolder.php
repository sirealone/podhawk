<?php

	$actiontype = array('backend');
	include 'authority.php';

	//reads and returns as a JSON object the contents of the upload folder

	$i = 0;
	$upload = array();
	$uploadfolder = opendir('../upload');

	while ($file = readdir($uploadfolder))
	{ 
		if (($file != "index.html") && (substr($file, 0, 1) != "."))
		{ 
		    $upload[$i] = $file; 
			$i++;
		}
	}

	closedir($uploadfolder);

	$the_array['files'] = $upload;

	include 'lang/' . $reg->findSetting('language') . '.php';

	$the_array['trans'] = $trans_updateUploadFolder;
	 
	$json = json_encode($the_array);
	
	//header("Content-type: application/json; charset=utf-8");
	echo $json;

?>
