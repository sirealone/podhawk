<?php

	$actiontype = array('backend');
	include 'authority.php';

	$message = "";
	$warning = FALSE;

	try
	{
		if ($reg->findSetting('acceptcomments') != 'akismet')
		{
			throw new Exception("no_akismet");
		}

		if (isset($_GET['do']))
		{
			try
			{
				if (!$authenticated)
				{
					throw new Exception('no_auth');
				}
		
				if ($_GET['do'] == 'clearall')
				{
					if ($currentUser->isAdmin() == false)
					{
						throw new Exception('Only administrators can delete all spam messages');
					}

					$dosql = "DELETE FROM ".DB_PREFIX."lb_spam";
					$GLOBALS['lbdata']->Execute($dosql);

				}

				//not spam? lets move it
				if ($_GET['do'] == "notspam" && isset($_GET['id']))
				{

					if (!$currentUser->mayEditComment($_GET['id'], 'lb_spam'))
					{
						throw new Exception('You cannot move this comment out of spam because you do not have edit privileges for the post');
					}

					require PATH_TO_ROOT. '/podhawk/lib/akismet.class.php';

					$preparedStatementArray = array(':id' => $_GET['id']);

					$dosql = "SELECT author, email, website, body, permalink, user_ip, user_agent, posted, message_html, posting_id FROM ".DB_PREFIX."lb_spam WHERE id = :id";

					$GLOBALS['lbdata']->prepareStatement($dosql);					

					$result = $GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

					$comment = array_slice($result[0],0,7);

					$akismet = new Akismet(THIS_URL, $reg->findSetting('akismet_key'), $comment); 

					if ($akismet->errorsExist())
					{
						throw new Exception('akismet_problem');
					}

					$akismet->submitHam();
					
					$dosql = "DELETE FROM ".DB_PREFIX."lb_spam WHERE id = :id";

					$GLOBALS['lbdata']->prepareStatement($dosql);

					$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

					$dosql = "INSERT INTO ".DB_PREFIX."lb_comments
					(name, mail, web, message_input, ip, message_html, posted, posting_id) VALUES
					(:name, :mail, :web, :message_input, :ip, :message_html, :posted, :posting_id)";

					$GLOBALS['lbdata'] -> prepareStatement($dosql);

					$preparedStatementArray = array(':name' 			=> $comment['author'],
													':mail' 			=> $comment['email'],
													':web' 				=> $comment['website'],
													':message_input' 	=> $comment['body'],
													':ip' 				=> $comment['user_ip'],
													':message_html' 	=> $result[0]['message_html'],
													':posted' 			=> $result[0]['posted'],
													':posting_id' 		=> $result[0]['posting_id']
													);							

					$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

					$clear->setFlag(array('SmartyCache'));

				} // end 'not-spam'

			} // close inner try block
			catch (Exception $e)
			{
				$message = $e->getMessage();
				$warning = true;
			}
		} // close if isset($_get['do'])

		// get everything from spam table, with each row linked to id and title of corresponding post
		$dosql = "SELECT " . DB_PREFIX . "lb_spam.*, " . DB_PREFIX . "lb_postings.title AS 'posting_title', "
				. DB_PREFIX . "lb_postings.id AS 'posting_id' 
				FROM ". DB_PREFIX . "lb_spam, " . DB_PREFIX . "lb_postings WHERE (" .
				DB_PREFIX . "lb_spam.posting_id = " . DB_PREFIX . "lb_postings.id)";
		$result = $GLOBALS['lbdata']->GetArray($dosql);

		$i = 0;
		foreach ($result as $row)
		{
			$result[$i]['may_delete'] = $currentUser->mayEdit($row['posting_id']);
			$i++;
		}

		$smarty->assign('spam', $result);
		$smarty->assign('spam_auth_key', $sess->createPageAuthenticator('spam'));

	} // close outer try block
	catch (Exception $e)
	{
		$message = $e->getMessage();
		$warning = true;
	}

	$smarty->assign('message', $message);
	$smarty->assign('warning', $warning);
?>
