<?php

$actiontype = array('backend');
include 'authority.php';


$message = "";
$problem = false;
$warning = false;
$file_to_delete = '';

// non-admin users may view this page, but can only amend or delete comments on posts which they can edit
try
{
	if (isset($_GET['do']) && isset($_GET['edit_id']))
	{	
		try
		{
			if(!$currentUser->mayEditComment($_GET['edit_id']))
			{
				throw new Exception("You cannot delete or edit this comment as you do not have edit privileges for the post.");
			}
			
								
			if (!$authenticated)
			{
				throw new Exception('no_auth');
			}

			if ($_GET['do'] == 'save')
			{
				$p = new HT_Comment();
				$message_html = $p->purify($_POST['commentmessage']);

				$preparedStatementArray = array(':author' 		=> entity_encode($_POST['commentname']),
												':body' 			=> entity_encode($_POST['commentmessage']),
												':message_html' 	=> $message_html,
												':id' 			=> $_GET['edit_id']);

				$dosql = "UPDATE " . DB_PREFIX . "lb_comments SET
							name = :author,
							message_input = :body,
							message_html = :message_html
							WHERE id = :id";
				
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

				$message = "Changes saved";

				$clear->setFlag(array('SmartyCache'));				
			}
		
			if ($_GET['do'] == 'spam')
			{
				$dosql = "SELECT * FROM ".DB_PREFIX."lb_comments WHERE id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['edit_id']));
				$row = $result[0];

				$comment = (array(	'author' 		=> $row['name'],
						 			'email' 		=> $row['mail'],
					 				'website' 		=> $row['web'],
					 				'body' 			=> $row['message_input'],
							 		'permalink' 	=> THIS_URL."/index.php?id=".$row['posting_id'],
							 		'user_ip' 		=> $row['ip'],
							 		'user_agent' 	=> $row['user_agent']
									));
		
			 	include PATH_TO_ROOT . "/podhawk/lib/akismet.class.php";

			 	$akismet = new Akismet(THIS_URL, $reg->findSetting('akismet_key'), $comment); 
			 	if(!$akismet->errorsExist())  $akismet->submitSpam();

				//delete from comments table
				$_GET['do'] = 'x';

			}// end spam actions

			//delete a comment
			if ($_GET['do'] == "x")
			{
		
				$dosql = "SELECT audio_file FROM ".DB_PREFIX."lb_comments WHERE id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['edit_id']));
	
				$row = $result[0];
				if (!empty($row['audio_file']))
				{
					$file_to_delete = $row['audio_file'];
				}

				$dosql = "DELETE FROM ".DB_PREFIX."lb_comments WHERE id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['edit_id']));

				$message = 'commentdeleted';

				$clear->setFlag(array('SmartyCache'));

				if (!empty($file_to_delete))
				{
					$_GET['do'] = 'delete_audio';
				}
			}

			// remove an audio file
			if ($_GET['do'] == 'delete_audio')
			{
				// find a file to delete, if we haven't inherited one from the previous section
				if (empty($file_to_delete))
				{
					$dosql = "SELECT audio_file FROM ".DB_PREFIX."lb_comments WHERE id = :id";
					$GLOBALS['lbdata']->prepareStatement($dosql);
					$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['edit_id']));
	
					$row = $result[0];
					if (!empty($row['audio_file']))
					{
						$file_to_delete = $row['audio_file'];
					}
				}

				// if we have a file to delete, and it actually exists, delete it
				if (!empty($file_to_delete) && file_exists($file_to_delete))

				{
					$permissions->make_writable ('audio');
					$permissions->make_writable(AUDIO_COMMENTS);

				   	$success = @unlink ($file_to_delete);

					$permissions->make_not_writable ('audio');
					$permissions->make_not_writable(AUDIO_COMMENTS);
				
					if (!$success) // if unlink() fails (eg permissions issues)
					{
						$log->write("Error in deleting audio comment file $file_to_delete in backend_comments.php");
						if ($message == 'commentdeleted')
						{
							throw new Exception("Comment {$_GET['edit_id']} deleted. However, I cannot delete the audio file $file_to_delete");
						}
						else
						{
							throw new Exception("I cannot delete audio file $file_to_delete.");
						}
					}
				}

				else // we have no record of a file to delete, or the file does not exist

				{
					// set a flag that we have not found a valid audio file to delete
					$file_to_delete = 'nofile';
				}

				// if we have not deleted the comment, amend the comment record to remove references to the audio file
				if ($message != 'commentdeleted')
				{
					$preparedStatementArray = array(':audio_file' 	=> '',
													':audio_type' 	=> '0',
													':audio_length' => '0:00',
													':audio_size' 	=> 0,
													':id' 			=> $_GET['edit_id']);

					$dosql = "UPDATE " . DB_PREFIX . "lb_comments SET 
								audio_file = :audio_file,
								audio_type = :audio_type,
								audio_length = :audio_length,
								audio_size = :audio_size
								WHERE id = :id";
				
					$GLOBALS['lbdata']->prepareStatement($dosql);
					$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

					$message = "I have deleted the audio file $file_to_delete.";

					if ($file_to_delete == 'nofile') // warn if the audio file was not found
					{
						$message = "I have not found an audio file to delete. I have removed the link to the audio file from the database.";
						$warning = true;
					}
					
				}
				else // we have already deleted the comment
				{
					if ($file_to_delete == 'nofile') // warn if the audio file was not found
					{
						$message = "I have deleted the comment. However, I cannot find the associated audio file. Perhaps it has been deleted already.";
					}
				}
				$clear->setFlag(array('SmartyCache'));		
			}
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$warning = true;
		}

		// ensure that these GETs are not carried forward in links in the pagination string
		unset ($_GET['do'], $_GET['edit_id']);		
		
	} // end isset $_GET['do']

	$pagination = new PO_Pagination_BackendComments();

	$showtable = $pagination->getRows();

	if (empty($message))
	{
		$message = $pagination->getMessage();
	}

	//add some useful information to the data about comments
	$i = 0;
	foreach ($showtable as $comment)
	{
		if (strlen($comment['message_input'] > 80))
		{
			$showtable[$i]['message_input'] = substr($comment['message_input'],0,80)."&hellip;";
		}

		$showtable[$i]['may_delete'] = $currentUser->mayEdit($comment['author_id']);
		$showtable[$i]['has_audio'] = false;
		if (!empty($showtable[$i]['audio_file']))
		{
			$showtable[$i]['has_audio'] = true;

			$ext = getExtension($showtable[$i]['audio_file']);
			$link = 'c' . $showtable[$i]['id'] . '.' . $ext;

			$showtable[$i]['audio_link'] = THIS_URL . "/get.php?com=$link";
		}
		$i++;
	}
	
	$smarty->assign(array(	'direction' 		=> $pagination->sorting->getTableHeadings(),
							'current_sort' 		=> $pagination->sorting->getSortField(),
							'sort_direction' 	=> $pagination->sorting->getSortDir(),
							'comments_list' 	=> $showtable,
							'paging_string' 	=> $pagination->getPaginationString($trans_comments['pages']),
							'akismet' 			=> ($reg->findSetting('acceptcomments') == 'akismet'),
							'comments_auth_key' => $sess->createPageAuthenticator('comments'),
							'record2_auth_key' 	=> $sess->createPageAuthenticator('record2'),
							'date_format'		=> $reg->findSetting('preferred_date_format')			
							));

}
catch (Exception $e)
{
	$message = $e->getMessage();
	$warning = true;
}

$smarty->assign('message', $message);
$smarty->assign('warning', $warning);

?>
