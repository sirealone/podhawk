<?php

	$actiontype = array('backend');
	include 'authority.php';

	$message = "";
	$warning = false;

	// make the audio folder writable
	$permissions->make_writable('audio');

	try
	{
		if (!$authenticated)
		{
			throw new Exception('no_auth');
		}

		if ($currentUser->mayEdit($_GET['id']) == false)
		{
			$message = 'You do not have the privileges to edit these tags';
		}

		$edit_id = $_GET['id'];
		$smarty->assign('edit_id', $edit_id);

		//get the filename from the posting-id we want to edit
		$dosql = "SELECT title, author_id, audio_file, audio_type, filelocal, summary FROM ".DB_PREFIX."lb_postings WHERE id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);		
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $edit_id));
		$fields = $result[0];

		$filename = AUDIOPATH . $fields['audio_file'];
		
		$smarty->assign('posting_title', $result[0]['title']);
		$smarty->assign('posting_summary', $result[0]['summary']);

		//Warning if remote file is to be changed :-)
		if ($fields['filelocal'] != "1")
		{ 
			throw new Exception('changeremoteid3');
		}		

			//update stuff, if requested by url
			if ((isset($_GET['do'])) AND ($_GET['do'] == "save"))
			{
				if ($currentUser->mayEdit($_GET['id']) == false)
				{
					throw new Exception('You do not have the privileges to edit these tags');
				}

				// initialise tagwriter
				$tagWriter = new ID_WriteID3($filename);

				if (!$tagWriter->setTagFormats($fields['audio_type']))
				{
					throw new Exception ('The getid3 programme cannot write tags for this type of file');
				}
				
				if ($tagWriter->writeData())				
				{ 
					$message = 'success';
				 }

				else
				{
					$message = 'failure';
				}

				$smarty->assign('warnings', $tagWriter->getErrors());

				$clear->setFlag(array('SmartyCache'));

			} // end save actions	

	}  // close try block

	catch (Exception $e)
	{
		$message = $e->getMessage();
		$warning = true;
		$filename = '';
	}

	$id3 = new ID_ReadID3 ($filename);

	$id3data = $id3->getBackendID3Data();

	// is there an image tag?
	$image_tag = false;

	if(!empty($id3data['image']))
	{	
			//we read the id3 image to the 'audio' folder as temp_image.jpg (or whatever)
			$tempfile = AUDIOPATH . "temp_image" . $id3data['imgtype'];

			if ($tempfileconn = @fopen($tempfile, 'wb'))
			{
				fwrite($tempfileconn, $id3data['image']);
				fclose($tempfileconn);
				$image_tag = true;
			}
				
	} //end image tag

	$disabled = ($currentUser->mayEdit($fields['author_id'])) ? '' : ' disabled="disabled"';

	// make the audio folder non-writable
	$permissions->make_not_writable('audio');

	$smarty->assign('image_tag', $image_tag);
	$smarty->assign('audio_type', $fields['audio_type']);
	$smarty->assign('disabled', $disabled);
	$smarty->assign('data', $id3data);
	$smarty->assign('message', $message);
	$smarty->assign('warning', $warning);
	$smarty->assign('id3_auth_key', $sess->createPageAuthenticator('id3'));
?>
