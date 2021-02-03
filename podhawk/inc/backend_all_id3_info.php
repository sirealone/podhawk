<?php

$actiontype = array('backend');
include 'authority.php';

$file = "";
$title = "";
	if (isset($_GET['id']))  
	{
		$dosql = "SELECT title, audio_file FROM ".DB_PREFIX."lb_postings where id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);		
		$row = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['id']));
		$file = $row[0]['audio_file'];
		$title = $row[0]['title'];

		 echo "<h3>".$trans_all_id3_info['info'].$file." (".$title.")</h3>";

		$r = new ID_ReadID3(AUDIOPATH . $file);
		$fileinfo = $r->getAllData();
	
		echo "<pre>";
		print_r ($fileinfo);
		echo "</pre>";
	}

	else
	{
		echo $trans_all_id3_info['no_file'];
	}
?>
