<?php

	$actiontype = array('backend');
	include 'authority.php';

	$sess = new SessionMaker();
	$sess->start();

	$login = new LoginManager();
	$cooks = new CookieMaker();

	//we want to logout? delete cookie and session
	if ((isset($_GET['do'])) AND ($_GET['do'] == 'logout'))
	{
	
		$login->logout();
		
		if (isset($_SESSION['authorid']))
		{
			$name = $reg->getNickname($_SESSION['authorid']);
			$events->write("$name logged out");
		}
		elseif  (isset($_COOKIE['phauth']))
		{
			$temp_id = $cooks -> checkCookie();
			$name = $reg->getNickname($temp_id);
			$events->write("$name logged out");
		}
		else
		{
			$events->write("Unknown user logged out");
		}		

		$cooks -> destroyCookie();
		$sess -> destroy();
	
		//start a new session (to enable a new login)
		$sess -> start();
		unset ($_SESSION['authorid']); //but make quite sure that the new session will not give access
	
	} //end log-out actions


	//log-in actions

	try
	{
		$access = false;
		$cookie_access = false;
		$cookie_rejected = false;
		$reason = "";
		$temp_id = "";
		$temp_login_name = "";
		$temp_name = "Unknown user";

		//if we do not have a minimum set of session variables....
		$test1 = (empty($_SESSION['authorid']) || empty($_SESSION['fingerprint']));
		//...and we do not have a posted login name and a posted password or challenge...
		$test2  = (empty($_POST['login_name']) || (empty($_POST['password']) && empty($_POST['challenge'])));
		//...and there is no cookie...
		$test3 = (empty($_COOKIE['phauth']));
		//...then you can't come in!
		if ($test1 && $test2 && $test3)
		{
			$access = false;

		}
		else // if we have a possible session, login or cookie to test
		{ 

		 	//if a session cookie has been sent by the browser, does the user-agent string sent by the browser match the session data?
			if (isset($_SESSION['fingerprint']))
			{
		 		$access = $sess -> validate();
			   			
				if(!$access)
				{
					$sess -> destroy();
				}
			
			}
			else // no session cookie
			{ 

				//if a login name is posted, can we match the $_POST data with a username/password from the database?	
				if (isset($_POST['login_name']))
				{
					//we accept logins only from authenticated pages
					if($sess->authenticate() == false)
					{
						$events->write('Attempted login from non-authenticated page.');
						@session_write_close();
						sleep(5);
						die("Hack attempt!");
					}

					$temp_id = $login->validateChallenge();
				
					if($temp_id)
					{
						$access = true;
						$temp_name = $reg->getNickname($temp_id);
					}
		 
					$reason = $login->getReason();
				
					$events->write($temp_name . $reason);

					//if login has failed, reset the session authorisation key and make the user wait for 5 seconds
					// (to hold up brute force attacks)
					if (!$access)
					{
						$sess->createSessionAuthKey();
						sleep(5);
					}	

				} //end if isset POST[login_name]

				//if we have failed to find a session or a valid login-name/password, is there a valid cookie?	
				elseif  (isset($_COOKIE['phauth']))
				{
					$temp_id = $cooks -> checkCookie();

					if ($temp_id)
					{
						$cooks -> regenerateCookie($temp_id);
						$access = true;
						$temp_name = $reg->getNickname($temp_id);
						$events->write("$temp_name logged in with cookie");

					}
					else
					{
						$cooks -> destroyCookie();
						$access = false;
						$events->write('Attempt to log in with invalid or out-of-time cookie');
					}

				} //end cookie actions

			}  //end 'no session cookie actions'

		} // end 'test session, login, cookie' actions

		if ($access)
		{
			//if successful login or cookie access, write useful information into session-data
		   	 if (empty($_SESSION['authorid']))
			{
				$sess -> setVariables($temp_id);	
			}

			//'remember me' for 7 days
			if ((isset($_POST['remember_me'])) && ($_POST['remember_me']==1))
			{
				$cooks -> makeCookie($temp_id);
				$events->write("New cookie created for $temp_name");		
			}

			//create new object to represent the logged-in user
			$currentUser = new US_User($_SESSION['authorid']);	
	
		}//end 'if-access'


		//just to be certain that the following cannot be re-accessed
	
		unset ($temp_login_name, $temp_id, $session_salt, $cooks);

		//but we don't unset the $sess or $login objects, as they are needed later

	} // close try block
	catch (Exception $e)
	{
		$access = false;
		unset ($_POST, $_GET);
		$cooks -> destroyCookie();
		$accessError = true;
		$log->write ("Exception thrown in accesscheck.php. Message : $e");
	}
	
?>
