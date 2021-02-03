<?php

$actiontype = array('backend');
include 'authority.php';

$warning = false;

try
{
	$playlist = new Playlist();
		
	$message = "";
	$filename = "";

	if (isset($_GET['action']))
	{			

		if (!$authenticated)
		{
			throw new Exception ('no_auth');
		}

		if (!empty($_POST['limit']))
		{
			$playlist->setLimit($_POST['limit']);
		}

		if (!empty($_POST['name']))
		{
			$playlist->setName($_POST['name']);
		}	

		switch ($_GET['action'])
		{
	
			case "ssv" :

				if (isset($_POST['ssv']))
				{
					$playlist->addSSV($_POST['ssv']);			
				}
				break;
		
			case "cat" :
	
				if (isset($_POST['category']))
				{
					$playlist->addCat($_POST['category']);
				}

				break;

			case "tag" :
		
				if (isset($_POST['tag']))
				{
					$playlist->addTag($_POST['tag']);
				}
				break;
		
		}
				
		$message = "in_upload_folder";

	}			

	$filename = $playlist->getFileName();
	$cats = $reg->getCategoriesArray();

	$playlist_auth_key = $sess->createPageAuthenticator('playlist');
	$record2_auth_key = $sess->createPageAuthenticator('record2');

	$audio_files = $playlist->findPlaylistFiles('audio');
	$upload_files = $playlist->findPlaylistFiles('upload');

	$smarty->assign(array(  'categories'        => $cats,
							'filename'          => $filename,
							'audio_files'       => $audio_files,
							'upload_files'      => $upload_files,
							'playlist_auth_key' => $playlist_auth_key,
							'record2_auth_key'  => $record2_auth_key));
		
}

catch (Exception $e)
{
	$message = $e->getMessage();
	$warning = true;
}

	$smarty->assign(array('message'=>$message, 'warning'=>$warning));

?>
