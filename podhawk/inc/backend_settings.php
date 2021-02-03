<?php

$actiontype = array('backend');
include 'authority.php';

try
{
	$warning = false;

	//check the rights
	if ($currentUser->isAdmin() == false)
	{ 
	 	throw new Exception('adminonly');
	}

	if (!isset($message)) $message = "";
	$problem = false;
	$comments_problem = false;

	//are we storing certain sensitive data in encrypted format?
	$crypto = (defined('BLOWFISH_KEY') && USE_BLOWFISH_ENCRYPTION == true);

	if($crypto)
	{ 
		$crypt_array = array('ftp_pass', 'ftp_user', 'amazon_access', 'amazon_secret');
		$blowfish = new SE_Blowfish;
	}

   	//get an array with the available themes
	
	$themes_contents = get_dir_contents('custom/themes');
	foreach ($themes_contents as $content)
	{
		//ignore hidden or back-up files
		if (substr($content,0,1) == "." || substr($content,-1) == "~") continue;

		//ignore the 'common_templates' directory
		if (trim($content) == 'common_templates') continue;

		$themes[] = $content;
	}

	natcasesort ($themes);
	
	try
	{
		if ($crypto)
		{		
			//encrypt ftp info
			foreach ($crypt_array as $crypt_item)
			{
				$_POST[$crypt_item] = isset($_POST[$crypt_item]) ? $blowfish->encrypt($_POST[$crypt_item]) : '';
			}

		}

		//put the posted data into the databse
		if (isset($_GET['do']))
		{
			// has the form returned a valid authenticator?
			if (!$authenticated)
			{
				throw new Exception('no_auth');
			}

			if ($_GET['do'] == 'save')
			{
				//take care of picture 1
				if (isset($_FILES['itunes_image']) && $_FILES['itunes_image']['size']<>"0")
				{	
					$permissions->make_writable('images');

					$newfilename = PATH_TO_ROOT . "/images/itunescover.jpg";

					if (move_uploaded_file($_FILES['itunes_image']['tmp_name'], $newfilename))
					{        
		   		 		chmod ($newfilename, 0644);
						$permissions->make_not_writable('images');
					}
					else
					{
						$permissions->make_not_writable('images');
						throw new Exception('uploadbroken'); 
					}				

				}

				//take care of picture 2
				if ((isset($_FILES['feedimage'])) AND ($_FILES['feedimage']['size'] <> "0"))
				{

					$permissions->make_writable('images');

					$newfilename = PATH_TO_ROOT . "/images/rssimage.jpg";
					if (move_uploaded_file($_FILES['feedimage']['tmp_name'], $newfilename))
					{         
						chmod ($newfilename, 0644);
						$permissions->make_not_writable('images');
					}
					else
					{
						$permissions->make_not_writable('images');
						throw new Exception('uploadbroken');
					}
				
				}

				//forms with a checkbox will not be posted if not checked :-(
				if (!isset($_POST['countweb'])) { $_POST['countweb'] = "0"; }
				if (!isset($_POST['countfla'])) { $_POST['countfla'] = "0"; }
				if (!isset($_POST['countpod'])) { $_POST['countpod'] = "0"; }

				//don't accept commenting without the necessary data
				if (($_POST['acceptcomments'] == 'loudblog'
					&& (empty($_POST['spamquestion']) || empty($_POST['spamanswer'])))
					OR
					($_POST['acceptcomments'] == 'akismet'
					&& empty($_POST['akismet_key']))
					OR
					($_POST['acceptcomments'] == 'disqus'
					&& empty($_POST['disqus_name'])))

				{
					$_POST['acceptcomments'] = 'none';

					throw new Exception('comments_problem');
				}
	
				//sort out which template language to put into the database	
				foreach ($themes as $theme)
				{
					if (isset($_POST[$theme."_template_language"]) && $_POST['template'] == $theme)
					{
						$_POST['template_language'] = $_POST[$theme."_template_language"];
					}
					unset ($_POST[$theme."_template_language"]);
				}

				if (!isset($_POST['template_language'])) $_POST['template_language'] = "none";

				// set up prepared statement to save settings data
				$dosql = "UPDATE " . DB_PREFIX . "lb_settings SET value = :value WHERE name = :name";
				$GLOBALS['lbdata']->prepareStatement($dosql);

				// save data from POST array
				foreach ($_POST as $setname => $setvalue)
				{

					if (substr($setname,0,4) != "id3_")
					{
						$setvalue = entity_encode($setvalue);
					}

					$preparedStatementArray = array(':value' => $setvalue, ':name' => $setname);
					$success = $GLOBALS['lbdata'] -> executePreparedStatement($preparedStatementArray);

					if(!$success)
					{
						throw new Exception('db_error');
					} 
				}
				
				$message = 'saved_ok';

				$clear->setFlag(array('SmartyCache', 'PHCache', 'Registry'));
			
			} //close saving actions	

			
		} //close isset($_GET['do'])

	} // end inner try block
	catch (Exception $e)
	{
		$warning = true;
		$message = $e->getMessage();
	}
	
	## BEGIN COLLECTING DATA FOR WEBPAGE

	//list the files in the language folder
	$lang_contents = get_dir_contents('lang');
	foreach ($lang_contents as $content)
	{
		if (substr($content, -4) == ".php")
		{
			$languages[] = substr($content,0,-4);
		}
	}
		   

	//get the available language options for each theme
	foreach ($themes as $theme)
	{
		$t = new TR_TranslationWebpage($theme);

		$theme_langs[$theme] = $t->getAvailableLangs();
	}

	$settings = $reg->refreshSettings();

	// decrypt ftp information
	if($crypto)
	{
		foreach ($crypt_array as $crypt_item)
		{
			$settings[$crypt_item] = $blowfish->decrypt($settings[$crypt_item]);
		}
	}
	
	//get info about categories
	$categories = $reg->getCategoriesArray();

	//is this a Windows machine?
	$windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

	// is PHP running under CGI/FastCGI?
	$cgi = (strpos(PHP_SAPI, 'cgi') !== FALSE);

	// do we display message about FTP layer?
	$displayFTP = (extension_loaded('FTP') && !$windows && !$cgi);

	//assign values to Smarty
	$smarty->assign(array(  'languages' 				=> $languages,
							'themes' 					=> $themes,
							'xml_ok' 					=> can_read_remote_xml(),
							'ftp_extension_loaded' 		=> $displayFTP,
							'db_type' 					=> DB_TYPE,
							'system_function_disabled' 	=> is_disabled('system'),
							'categories' 			 	=> $categories,
							'theme_langs' 				=> $theme_langs,
							'settings' 				 	=> $settings,
							'settings_auth_key' 	 	=> $sess->createPageAuthenticator('settings'),
							'testFTP_auth_key'  	 	=> $sess->createPageAuthenticator('testFTP'),
							'itunescats'        	 	=> array_flip($trans_itunescats),
							'itunes_languages'  	 	=> $trans_langs,
							'upload_limit' 				=> uploadlimit()));

}// close outer try block
catch (Exception $e)
{
	$warning = true;
	$message = $e->getMessage();
}

$smarty->assign('message', $message);
$smarty->assign('warning', $warning);

?>
