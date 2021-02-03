<?php

	$actiontype = array('backend');
	include 'authority.php';

	$message = "no_audio";
	$warning = false;
	$ping = false;
	$edit_id = "";

	//we need a sanity clause. You're kidding. There ain't no Sanity Clause!
	//OK, that was from the Marx Brothers, A Night at the Opera
	try
	{ 
		if (isset($_GET['id']))
		{
			$dosql = "SELECT id FROM ".DB_PREFIX."lb_postings WHERE id = ".$_GET['id'];
			$result = $GLOBALS['lbdata']->GetArray($dosql);

			if(empty($result[0]['id']))
			{
				throw new Exception('we_have_problem');				
			}
		}

		if (!isset($_GET['do']))
		{
			throw new Exception('no_action');
		}	

		try
		{
			$update_id = (isset($_GET['id'])) ? $_GET['id'] : false;

			switch ($_GET['do'])
			{

				case 'browser' :
					$permissions->make_writable('audio');
					$p = new RE_UploadBrowser($update_id);
					$p -> makePosting();
					$edit_id = $p->getId();
					$message = $p->getMessage();
					$permissions->make_not_writable('audio');
					break;

				case 'web' :
					if ($_POST['method'] == 'link')
					{
						$p = new RE_LinkWeb($update_id);
						$p->makePosting();
						$edit_id = $p->getId();
						$message = $p->getMessage();
					}
					elseif ($_POST['method'] == 'copy')
					{
						$permissions->make_writable('audio');
						$p = new RE_FetchWeb($update_id);
						$p->makePosting();
						$edit_id = $p->getId();
						$message = $p->getMessage();
						$permissions->make_not_writable('audio');
					}
					break;

				case 'transfer' :
					$permissions->make_writable('audio');
					$permissions->make_writable('upload');
					$p = new RE_CopyFTP($update_id);
					$p->makePosting();
					$edit_id = $p->getId();
					$message = $p->getMessage();
					$permissions->make_not_writable('upload');
					$permissions->make_not_writable('audio');
					break;

				case 'nofile' :
					$p = new RE_NoAudio($update_id);
					$p->makePosting();
					$edit_id = $p->getId();
					$message = $p->getMessage();
					break;

				case 'edit' :
					if (!isset($_GET['id'])) break;					
					$edit_id = $_GET['id'];
					// we get the message later
					break;

				case 'save' :
					if (!isset($_GET['id'])) break;	
					$edit_id = $_GET['id'];
					$message = 'saved';
					break;

				case 'change_editor' :
					if (!isset($_GET['id'])) break;	
					$edit_id = $_GET['id'];
					$message = 'editor_change';
					break;

				case 'jw_link' :
					$p = new RE_JwExternalLink($update_id, $trans_record1['url_here']);
					$p->makePosting();
					$edit_id = $p->getId();
					$message = $p->getMessage();
					break;

				default :
					if (!isset($_GET['id'])) break;	
					$edit_id = $_GET['id'];
					$message = 'no_action';
					break;
			
			} //end switch
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$warning = true;
		}
	
		if (empty($edit_id))
		{
			throw new Exception('I cannot find a posting ID');
		}	

		

		//saving actions ###########################################
		############################################################
		
		if($_GET['do'] == "save")
		{
			try
			{ 
				if (!$authenticated)
				{
					throw new Exception('no_auth');
				}

				try
				{
					// deal with Amazon upload/download and make database changes as needed				
					if (isset($_POST['amazon_upload']))
					{
						if (isset($_POST['filelocal']) && $_POST['filelocal'] == 1) // do we have a local file to upload?
						{
							$am = new RE_Amazon();
							$am->upload($_POST['audio_file'], $edit_id);
							$events->write("File {$_POST['audio_file']} uploaded to Amazon S3.");
							$smarty->assign('message2', 'amazon_upload'); 
						}
					}

					if (isset($_POST['amazon_download']))
					{
						if (RE_Amazon::isAmazon($_POST['audio_file'])) // do we have a file on Amazon S3 to download?
						{
							$am = new RE_Amazon();
							$am->download($_POST['audio_file'], $edit_id);
							$events->write("File {$_POST['audio_file']} downloaded from Amazon S3 to audio directory.");
							$smarty->assign('message2', 'amazon_download');
						}		
					}
				} // close inner try block

				catch (PodhawkException $e)
				{
					$smarty->assign('message2', $e->getmessage());
					$warning = true;
					$log->write('Amazon S3 error. ' . $e->getMessage());
				}

				// trigger onSavePosting event and process any requests to modify variables
				$h = $plugins->event("onSavePosting", $edit_id);

				if ($h)
				{
					foreach ($h as $j) rewriteVariables($j);
				} 	

				$save = new RE_SavePosting($edit_id, $currentUser);

				$success = $save->makePosting();

				$message = $save->getMessage();

				$previousStatus = $save->getPreviousStatus();

				// if we are putting a new posting on air,
				//trigger the onPing event and tell plugins the id and title of the new post
				if ($success == true && $previousStatus < 3 && $_POST['status'] == 3)
				{
					$h = $plugins->event("onPing", array($edit_id, $_POST['title']));
					if ($h)
					{
						foreach ($h as $j) rewriteVariables($j);
					}

					$events->write("Posting $edit_id {$_POST['title']} on air.");
				}

				// update any updateable playlist files
				$playlist = new Playlist();

				$playlist -> update();

				$clear->setFlag(array('SmartyCache', 'PHCache'));			 

			} // close inner try block

			catch (Exception $e)
			{
				$message = $e->getMessage();
				$warning = true;
			}
		}
		//end save actions####################################
		########################################################

		try
		{
			// get the information needed about the posting
			$posting = new PO_Posting_BackendRecord2($edit_id);

			$posting->extendPostingData();

			$fields = $posting->getPosting();

			$links = $posting->getAssociatedLinks();

			$editor_to_use = $posting->getEditor();

	
			//an array of possible audio comment sizes
			$tempcommsize = array(
					"0"         => $trans_record2['noaudioallowed'],
					"204800"     => "200 KB",
					"512000"    => "500 KB",
					"1048576"    => "1 MB",
					"1572864"   => "1.5 MB",
					"2097152"   => "2 MB",
					"5242880"   => "5 MB",
					"10485760"  => "10 MB",
					"999999999" => $trans_record2['nolimit']);
	
			// can the current user edit or publish the post?
			$may_edit 		= $currentUser->mayEdit($fields['author_id']);
			$may_publish 	= $currentUser->mayPublish($fields['author_id']);
			$may_edit_all	= $currentUser->mayEditAll();

			$authors = $reg->getAuthorsList();
			
			// nice touch - lets get $authors into alphabetical order of nicknames
			function compareByNickname($a, $b)
			{
				return strcmp($a["nickname"], $b["nickname"]);
			}
			uasort ($authors, 'compareByNickname');

			$postAuthor = $fields['author_id'];

			// create the message to display on the screen
			if (isset($_GET['do']) && $_GET['do'] == 'edit')
			{		
				$edit_message 		= ($may_edit) ? 'editready' : 'cannot_edit';
				$publish_message 	= ($may_publish) ? 'may_publish' : 'may_not_publish';
				$message 			= $edit_message . "_" . $publish_message;
			}

			//a convenient way of disabling form elements
			$readonly 	= ($may_edit) 		? '' : 'disabled="disabled"'; // disable all form elements if user cannot edit
			$readonly2 	= ($may_edit_all) 	? '' : 'disabled="disabled"'; // disable authors box if users cannot edit all (ie user cannot post as another user)
			$readonly3 	= ($may_publish) 	? '' : 'disabled="disabled"'; // disable 'on air' if user cannot publish
			$readonly4  = ($may_edit || $may_publish) ? '' : 'disabled="disabled"';	// disable 'draft' and 'finished' if user cannot edit and cannot publish		

			// if the editor has changed, we should turn autosave off, to avoid saving posting in an inappropriate format
			$autosave_temp = ($fields['edited_with'] > 0 && isset($_POST['editor_requested']) && $_POST['editor_requested'] != $fields['edited_with']) ? 'false' : 'true';

			// trigger a posting data ready event
			$h = $plugins->event("onBackendPostingDataReady", $fields);

			if ($h)
			{
				foreach ($h as $j) rewriteVariables($j);
			}

			if ($fields['filelocal'] == '1')
			{
				$tagReader = new ID_ReadID3 (AUDIOPATH . $fields['audio_file']);

				$id3 = $tagReader->getBackendRecord2Data();
			}
			else
			{
				$id3 = '';
			}
			
			$record2_auth_key = $sess->createPageAuthenticator('record2');

			$smarty->assign(array(  'posting'            => $fields,
									'links'              => $links,
									'links_to_show'      => $reg->findSetting('showlinks'),
									'id3'                => $id3,
									'posted_date'        => strtotime($posting->getCol('posted')),
									'id'                 => $edit_id,
									'cats'               => $reg->getCategoriesArray(),
									'this_cats'          => $posting->getCategories(),
									'comment_size'       => $tempcommsize,
									'ping'               => $ping,
									'preview'            => $reg->findSetting('previews'),
									'readonly'           => $readonly,
									'readonly2' 		 => $readonly2,
									'readonly3'			 => $readonly3,
									'readonly4'			 => $readonly4,
									'may_edit'           => $may_edit,
									'may_publish'        => $may_publish,
									'edited_with'        => DataTables::findEditor($fields['edited_with']),
									'editor_to_use'      => $editor_to_use,
									'edit_date'          => $fields['edit_date'],
									'record2_auth_key'   => $record2_auth_key,
									'id3_auth_key'       => $sess->createPageAuthenticator('id3'),
									'autosave_auth_key'	 => $sess->createPageAuthenticator('autosave'),
									'imagelist_auth_key' => $sess->createPageAuthenticator('imagelist'),
									'autosave' 			 => $reg->findSetting('autosave'),
									'autosave_temp'		 => $autosave_temp,
									'amazon_available'	 => $reg->amazonAvailable(),
									'authors'			 => $authors,
									'my_id'				 => $postAuthor,
									'acceptcomments'	 => $reg->findSetting('acceptcomments')
								));
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$warning = true;
		}
		## additional menu items for Recording page 2
		#############################################
		try
		{
			//preview link
			if($reg->findSetting('previews') == 1)
			{
				$preview_link = "<a href=\"". THIS_URL . "/index.php?id=$edit_id&amp;preview=1\" target=\"_blank\">{$trans_menu['preview']}</a>";
				$smarty->append('green_menu', $preview_link);
				$smarty->append('green_menu', "<a href=\"http://validator.w3.org/check?uri=" .
												urlencode(THIS_URL."/index.php?preview=1&id=$edit_id") .
												"\" target=\"_blank\"/>Validate HTML</a>");
			}

			//find the next posting
			$dosql = "SELECT  id, title, posted FROM ".DB_PREFIX."lb_postings
				  WHERE posted = (SELECT MIN(posted) from ".DB_PREFIX."lb_postings WHERE posted > '".$fields['posted']. "')";

			$nextPosting = $GLOBALS['lbdata']->GetArray($dosql);

			if(!empty($nextPosting))
			{
				$smarty->append('green_menu', "<a href = \"?page=record2&amp;do=edit&amp;id={$nextPosting[0]['id']}&amp;auth=$record2_auth_key\" title = \"{$nextPosting[0]['title']} : id={$nextPosting[0]['id']} : posted {$nextPosting[0]['posted']}\">{$trans_menu['nextpost']}</a>");

			}

			//and the previous posting
			$dosql = "SELECT  id, title, posted FROM ".DB_PREFIX."lb_postings
					  WHERE posted = (SELECT MAX(posted) from ".DB_PREFIX."lb_postings
						      WHERE posted < '".$fields['posted']. "')";

			$previousPosting = $GLOBALS['lbdata']->GetArray($dosql);

			if(!empty($previousPosting))
			{
				$smarty->append('green_menu', "<a href = \"?page=record2&amp;do=edit&amp;id={$previousPosting[0]['id']}&amp;auth=$record2_auth_key\" title = \"{$previousPosting[0]['title']} : id={$previousPosting[0]['id']} : posted {$previousPosting[0]['posted']}\">{$trans_menu['prevpost']}</a>");
			}

			//and the most recent posting
			$dosql = "SELECT  id, title, posted FROM ".DB_PREFIX."lb_postings
				   WHERE posted = (SELECT MAX(posted) from ".DB_PREFIX."lb_postings)";

			$latestPosting = $GLOBALS['lbdata'] -> GetArray($dosql);
			if((!empty($latestPosting)) && ($latestPosting[0]['id'] != $edit_id))
			{
				$smarty->append('green_menu', "<a href = \"?page=record2&amp;do=edit&amp;id={$latestPosting[0]['id']}&amp;auth=$record2_auth_key\" title = \"{$latestPosting[0]['title']} : id={$latestPosting[0]['id']} : posted {$latestPosting[0]['posted']}\">{$trans_menu['latestpost']}</a>");
			}

			// link to comments
			$comments = new PO_Posting_Comments($edit_id);
			$countComments = $comments->getCount();
			if ($countComments > 0)
			{
				$smarty->append('green_menu', "<a href=\"index.php?page=comments&amp;posting_id=$edit_id\" title=\"". $countComments . " comments\">{$trans_menu['commentshere']}</a>");
			 }
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
		}
		
	} //close the outer try block
	catch (Exception $e)
	{
		$message = $e->getMessage();
		$warning = true;
	}

	$smarty ->assign('message', $message);
	$smarty ->assign('warning', $warning);
?>
