<?php

	$actiontype = array('backend');
	include 'authority.php';

	$message = "";
	$warning = false;

	try
	{

		//only administrators can access this page
		if ($currentUser->isAdmin() == false)
		{
			throw new Exception('adminonly');
		}

		//delete author if requested, but not if the author is yourself!
		if (isset($_GET['do']) && $_GET['do'] == "delauthor")
		{
			try
			{
				if (!$authenticated)
				{
					throw new Exception('no_auth');
				}
				
				if ($currentUser->isMe($_GET['id']))
				{
					throw new Exception('adminsuicide');
				}

				$condemnedAuthor = $reg->getNickname($_GET['id']);

				if ($condemnedAuthor == false)
				{
					throw new Exception ("Author id {$_GET['id']} not found");
				}
			
			   	$dosql = "DELETE FROM ".DB_PREFIX."lb_authors WHERE id = :id";
				$GLOBALS['lbdata']->prepareStatement($dosql);
				$GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['id']));

				$message = "I have deleted $condemnedAuthor";

				$clear->setFlag(array('SmartyCache', 'PHCache', 'Registry'));
			}
			catch (Exception $e)
			{
				$message = $e->getMessage();
				$warning = true;
			}
		}

		$smarty->assign('authors', $reg->refreshAuthors());
		$smarty->assign('authors1_auth_key', $sess->createPageAuthenticator('authors1'));
		$smarty->assign('authors2_auth_key', $sess->createPageAuthenticator('authors2'));

	}
	catch (Exception $e)
	{
		$message = $e->getMessage();
		$warning = true;
	}

	$smarty->assign('message', $message);
	$smarty->assign('warning', $warning);

?>
