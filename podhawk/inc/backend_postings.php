<?php

$actiontype = array('backend');
include 'authority.php';

if (isset($_GET['do']) && $_GET['do'] == 'allposts') unset ($_SESSION['find_criterion']);

$warning = false;
 
//delete data in filesystem and database, if required by url!
if (isset($_GET['do']) && $_GET['do'] == "x")
{
	try
	{
		if (!$authenticated)
		{
			throw new Exception('no_auth');
		}

		if (isset($_GET['id']))
		{		
			$array = array(':id' => $_GET['id']);
		
			//delete audio file
			$dosql = "SELECT filelocal, audio_file FROM ".DB_PREFIX."lb_postings WHERE id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$result = $GLOBALS['lbdata']->executePreparedStatement($array);

			if (isset($result[0]))
			{    
				$row = $result[0];
	
				// if we have a local audio file, unlink it	
				if ($row['filelocal'] == 1)
				{
					$deletepath = AUDIOPATH . $row['audio_file'];
					$permissions->make_writable('audio');
					@unlink ($deletepath);
					$permissions->make_not_writable('audio');
				}

				//else if the file is on Amazon S3, delete it

				if (RE_Amazon::isAmazon($row['audio_file']) && $reg->amazonAvailable())
				{
					try
					{
						$amazon = new RE_Amazon();
						$success = $amazon->deleteAmazonObject($row['audio_file']);

						if (!$success)
						{
							throw new PodhawkException ("Failed to delete file {$row['audio_file']} from Amazon bucket.");
						}
						$events->write ("Deleted {$row['audio_file']}");
					}
					catch (PodhawkException $e)
					{
						$message = "I have removed posting {$_GET['id']} from the database, but I cannot delete the audio file {$row['audio_file']} from the Amazon bucket.";
						$log->write ($e->getMessage());
						$warning = true;
					}					
				}

				// delete the record in the postings table...
				$dosql = "DELETE FROM ".DB_PREFIX."lb_postings WHERE id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$GLOBALS['lbdata']->executePreparedStatement($array);
				$events->write ("Deleted posting {$_GET['id']} from database.");		
		
				// find any related audio comments and unlink them
				$dosql = "SELECT audio_file FROM ".DB_PREFIX."lb_comments WHERE posting_id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$result = $GLOBALS['lbdata']->executePreparedStatement($array);

				$notDeleted = array();

				if (!empty($result))
				{
					$permissions->make_writable('audio');
					$permissions->make_writable(AUDIO_COMMENTS);

					foreach ($result as $row)
					{					
						$success = @unlink ($row['audio_file']);
						if (!$success)
						{
							$notDeleted[] = $row['audio_file'];
						}
					}

					$permissions->make_not_writable('audio');
					$permissions->make_not_writable(AUDIO_COMMENTS);
				}

				// delete any related records in the comments table	
				$dosql = "DELETE FROM ".DB_PREFIX."lb_comments WHERE posting_id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$GLOBALS['lbdata']->executePreparedStatement($array);
			
				if (!empty($notDeleted))
				{
					throw new Exception("Failed to delete the following comment audio files - " . implode(',', $notDeleted));
				}

				$message = 'deleted';
	
			}
		}
		$clear->setFlag(array('SmartyCache', 'PHCache'));
		
	}
	catch (Exception $e)
	{
		$log->write("Error deleting posting {$_GET['id']} from database. Error message was {$e->getMessage()}");
		$message = $e->getMessage();
		$warning = true;
	}	
}
//getting all sql-data needed for the table
$pagination = new PO_Pagination_BackendPostings();

$showtable = $pagination->getRows();

if (empty($message))
{
	$message = $pagination->getMessage();
}

if (!empty($showtable))
{
	foreach ($showtable as $post)
	{
		$posting = new PO_Posting_BackendPostings($post);
		$posting->getTranslationTable(array('draft'		=>$trans_postings['draft'],
											'finished' 	=> $trans_postings['finished'],
											'onair' 	=> $trans_postings['onair']));
		$posting -> extendPostingData();
		$post_table[] = $posting->getPosting();
	}
 
	$smarty->assign(array(	'paging_string' 	=> $pagination->getPaginationString($trans_postings['pages']),
							'current_sort'		=> $pagination->sorting->getSortField(),
							'sort_direction' 	=> $pagination->sorting->getSortDir(),			
							'direction' 		=> $pagination->sorting->getTableHeadings(),
							'post_table' 		=> $post_table,
							'postings_auth_key' => $sess->createPageAuthenticator('postings'),
							'record2_auth_key' 	=> $sess->createPageAuthenticator('record2')));
}

if (!isset($message)) $message = "";
$smarty->assign('message',$message);
$smarty->assign('warning', $warning);
?>
