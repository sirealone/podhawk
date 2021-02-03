<?php

$actiontype = array('backend');
include 'authority.php';

try
{
	//only administrators can access this page
	if ($currentUser->isAdmin() == false)
	{
		throw new Exception('adminonly');
	}

	$author_id = "";
	$message = "";
	$warning = false;

	//we need an id for the author
	if(isset($_GET['id']))
	{
		$author_id = $_GET['id'];
	}

	//but if she/he is a new author, we need to create a database entry first and then find the id
	if ((isset($_GET['do'])) && ($_GET['do'] == 'newauthor'))
	{
		if (!$authenticated)
		{
			throw new Exception ('no_auth');
		}

		$tempdate = date('Y-m-d H:i:s');

		$dosql = "INSERT INTO ".DB_PREFIX."lb_authors
				(joined, nickname, login_name, realname, mail, password,
				edit_own, publish_own, edit_all, publish_all, admin, hide)   
				VALUES
				(:joined, :nickname, :login_name, :realname, :mail, :password,
				:edit_own, :publish_own, :edit_all, :publish_all, :admin, :hide)";

		$GLOBALS['lbdata']->prepareStatement($dosql);

		$preparedStatementArray = array(':joined' 		=> $tempdate,
										':nickname' 	=> entity_encode($_POST['newnick']),
										':login_name' 	=> entity_encode($_POST['newnick']),
										':realname' 	=> entity_encode($_POST['newname']),
										':mail' 		=> entity_encode($_POST['newmail']),
										':password' 	=> '',
										':edit_own' 	=> '1',
										':publish_own' 	=> '0',
										':edit_all' 	=> '0',
										':publish_all' 	=> '0',
										':admin' 		=> '0',
										':hide'			=> '0');

		$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

		//find the id of the new entry
		$dosql = "SELECT id from ".DB_PREFIX."lb_authors WHERE joined = :joined";
		$GLOBALS['lbdata'] -> prepareStatement($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':joined' => $tempdate));

		$author_id = $result[0]['id'];

		$message = "newauthorcreated";
		
	}// close create new author

	//if, despite the above, we still have no id, then something is wrong!
	if (empty($author_id))
	{
		throw new PodhawkException('no_id');
	}

	//perhaps we need to save some revised author data
	if ((isset($_GET['do'])) && ($_GET['do'] == 'saveauthor'))
	{
		try
		{
			if (!$authenticated)
			{
				throw new Exception('no_auth');
			}
			
			//where check-boxes were not checked, set values to zero
			$edit_own = (isset($_POST['edit_own'])) ? '1' : '0';
			$publish_own = (isset($_POST['publish_own'])) ? '1' : '0';
			$edit_all = (isset($_POST['edit_all'])) ? '1' : '0';
			$publish_all = (isset($_POST['publish_all'])) ? '1' : '0';
			$admin = (isset($_POST['admin'])) ? '1' : '0';
			$hide = (isset($_POST['hide'])) ? '1' : '0';

			//however, an admin user cannot degrade herself
			if ($admin == '0' && $currentUser->isMe($author_id))
			{
			   	throw new PodhawkException('admindegrade');
			}

			$preparedStatementArray = array(':nickname' 	=> entity_encode($_POST['at_nickname']),
											':login_name' 	=> entity_encode($_POST['at_login_name']),
											':realname' 	=> entity_encode($_POST['realname']),
											':mail' 		=> entity_encode($_POST['mail']),
											':edit_own' 	=> $edit_own,
											':publish_own' 	=> $publish_own,
											':edit_all' 	=> $edit_all,
											':publish_all' 	=> $publish_all,
											':admin'		=> $admin,
											':hide'			=> $hide,
											':id' 			=> $author_id
											);

			if ($_POST['new_password'] === $_POST['new_password2'])
			{
				$putpass = "";

				if (empty($_POST['new_password'])) // we are deleting a password, not resetting it
				{
					$dosql = "UPDATE " . DB_PREFIX . "lb_authors SET password = '' WHERE id = :id";
					$GLOBALS['lbdata']->prepareStatement($dosql);

					$GLOBALS['lbdata']->executePreparedStatement(array(':id' => $author_id));
				}
					 
				//prepare password-change
				elseif ($_POST['new_password'] != "default")
				{
					$preparedStatementArray[':password'] = md5($_POST['new_password']);
					$putpass = 'password = :password, ';   
				}
				
				
				$dosql = "UPDATE " . DB_PREFIX . "lb_authors SET
						$putpass
						nickname = :nickname,
						login_name = :login_name,
						realname = :realname,
						mail = :mail,
						edit_own = :edit_own,
						publish_own = :publish_own,
						edit_all = :edit_all,
						publish_all = :publish_all,
						admin = :admin,
						hide = :hide
						WHERE id = :id";

				$GLOBALS['lbdata']->prepareStatement($dosql);

				$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);
			
				$message = 'savesuccess';

			}
			else
			{ 
				if ($_POST['new_password'] != $_POST['new_password2'])
				{
					throw new PodhawkException('errorpassconfirm');
				}
			}

			$clear->setFlag(array('SmartyCache', 'PHCache', 'Registry'));

		} // close inner try block
		catch (PodhawkException $e)
		{
			$message = $e->getMessage();
			$warning = true;
		}
			
	} // close save author actions

	//getting data for requested author-id from authors-table
	$dosql = 'SELECT * FROM ' . DB_PREFIX . 'lb_authors WHERE id = :id';
	$GLOBALS['lbdata']->prepareStatement($dosql);
	$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $author_id));

	if(empty($result[0]))
	{
		throw new Exception('noauthordata');
	}

	$passwordNotSet = (empty($result[0]['password']));
	unset ($result[0]['password']); // dont send the hashed password to the template
	$smarty->assign('author', $result[0]);
	$smarty->assign('no_password', $passwordNotSet);

	if ($result[0]['nickname'] == $result[0]['login_name'])
	{
		throw new Exception('setnewlogin');
	}

	if ($passwordNotSet && empty($message))
	{
		throw new Exception('nopassword');
	}

} //close outer try block
catch (Exception $e)
{	
	$message = $e->getMessage();
	$warning = true;	
}

$smarty->assign(array(	'authors2_auth_key' => $sess->createPageAuthenticator('authors2'),
						'message' 			=> $message,
						'warning' 			=> $warning));


?>
