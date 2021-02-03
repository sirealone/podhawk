<?php

$actiontype = array('backend');
include 'authority.php';

$message = '';
$warning = false;

if (isset($_GET['action']))
{
	try
	{
		if (!$authenticated)
		{
			throw new Exception('no_auth');
		}

		$p = new Permissions(array('audio', 'upload'));
		$p->make_writable('audio');
		$p->make_writable('upload');

		if ($_GET['action'] == 'addFile')
		{
			$add = new RE_NewAddFile(false);

			$add->makePosting();

			$message = $add->getmessage();
			$clear->setFlag(array('SmartyCache'));				
		}
	
		if ($_GET['action'] == 'removeFile')
		{
			$dosql = "SELECT addfiles FROM " . DB_PREFIX . "lb_postings WHERE id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$array = array (':id' => $_GET['id']);
			$result = $GLOBALS['lbdata']->executePreparedStatement($array);

			$addfiles = unserialize ($result[0]['addfiles']);
			$toRemove = trim($_POST['fileToRemove']);

			foreach ($addfiles as $key => $file)
			{
				if ($file['name'] == $toRemove)
				{
					unset ($addfiles[$key]);
					@unlink (AUDIOPATH . $toRemove);
				}
			}

			$dosql = "UPDATE " . DB_PREFIX ."lb_postings SET addfiles = :addfiles WHERE id = :id";
			$GLOBALS['lbdata'] -> prepareStatement($dosql);
			$array = array(':addfiles' => serialize($addfiles),
							':id' => $_GET['id']);
			$GLOBALS['lbdata'] -> executePreparedStatement($array);

			$clear->setFlag(array('SmartyCache'));
		}

		if ($_GET['action'] == 'addImage' || $_GET['action'] == 'removeImage')
		{
			$imageName = ($_GET['action'] == 'addImage') ? trim($_POST['imageToAdd']) : '';
			if (strpos($imageName, '/') !== false) // if the image name contains '/' it is presumably not in the images folder
			{
				throw new Exception('You can only attach an image in your images folder to the posting.');
			} 
			$dosql = "UPDATE " . DB_PREFIX . "lb_postings SET image = :image WHERE id = :id";
			$array = array(':image' => $imageName,
							':id' => $_GET['id']);
			$GLOBALS['lbdata'] -> prepareStatement($dosql);
			$GLOBALS['lbdata'] -> executePreparedStatement($array);

			$clear->setFlag(array('SmartyCache'));			
		}

		$p->make_not_writable('upload');
		$p->make_not_writable('audio');
	}
	
	catch (Exception $e)
	{
		$message = $e->getMessage();
		$warning = true;
	}
} // end actions

// assign stuff to Smarty

// posting data
$dosql = "SELECT id, title, audio_type, image, addfiles from " . DB_PREFIX . "lb_postings WHERE id = :id";
$GLOBALS['lbdata']->prepareStatement($dosql);
$postings = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['id']));
$posting = $postings[0];
$posting['addfiles'] = (!empty($posting['addfiles'])) ? unserialize ($posting['addfiles']) : '';
$smarty->assign('posting', $posting);

// are addfiles permitted for this file type?
$data = DataTables::AudioTypeData($posting['audio_type']);
$smarty->assign('addfilesAllowed', $data['addfiles']);

// upload folder
$upload = get_dir_contents(PATH_TO_ROOT . '/upload');

//..except for index.html/index.php
foreach ($upload as $index=>$value)
{
	if ($value == 'index.html' || $value == 'index.php')
	{
		unset ($upload[$index]);
	}
}
$smarty->assign('upload', $upload);

// authorisation keys

$smarty->assign('addfiles_auth_key', $sess->createPageAuthenticator('addfiles'));

// message
$smarty->assign (array(	'message' => $message,
						'warning' => $warning));
				
?>
