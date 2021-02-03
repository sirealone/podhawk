<?php

$actiontype = array('backend');
include 'authority.php';

$warning = false;
$message = '';

$sqlite_db = (file_exists(SQLITE_DIR . 'loudblogdata.db')) ? SQLITE_DIR . 'loudblogdata.db' : AUDIOPATH . 'loudblogdata.db';
$sqlite_htaccess = SQLITE_DIR . '.htaccess';

$user = $currentUser->getNickname();

try
{
	$warning = false;

	//check the rights
	if ($currentUser->isAdmin() == false)
	{ 
	 	throw new Exception('adminonly');
	}

	if (isset($_GET['do']))
	{

		if (!$authenticated)
		{
			throw new Exception('no_auth');
		}
	
		if ($_GET['do'] == 'check_updates')
		{
			$u = new XM_UpdateCheck("http://www.podhawk.com/release.xml");

			$message = $u->check();
		}

		if ($_GET['do'] == 'backup_database')
		{
			$permissions->make_writable('audio');

			$mailer = new MA_DBBackup();

			$message = $mailer->sendBackup();

			$permissions->make_not_writable('audio');

			$events->write($user . ' made MySQL database backup.');
		}

		if ($_GET['do'] == 'open_sqlite')
		{
			$chmod = chmod (SQLITE_DIR, 0777);
			$chmod1 = chmod ($sqlite_db, 0777);

			if (!$chmod || !$chmod1)
			{
				throw new Exception ('Sorry! I cannot make the SQLite directory writeable');
			}

			if (file_exists($sqlite_htaccess))
			{
				unlink ($sqlite_htaccess);	
			}

			$message = "sqlite_opened";

			$events->write($user . ' made SQLite directory and SQLite database file world writeable.');
		}

		if ($_GET['do'] == 'close_sqlite')
		{
			$chmod = chmod (SQLITE_DIR, 0755);
			$chmod1 = chmod ($sqlite_db, 0600);

			if (!$chmod || !$chmod1)
			{
				throw new Exception ('Sorry! I cannot make the SQLite directory non-writeable');
			}

			$htaccess_text =<<<EOF
# Prevent access from outside web root
order deny, allow
deny from all
EOF;

			if (!file_exists($sqlite_htaccess) && is_writable(SQLITE_DIR))
			{
				$h = fopen ($sqlite_htaccess, 'wb');
				fwrite($h, $htaccess_text);
				fclose ($h);		
			}

			$message = "sqlite_closed";

			$events->write($user . ' restored normal permissions for SQLite directory and database file.');

		}

		if ($_GET['do'] == 'backup_sqlite')
		{
			$permissions->make_writable('audio');

			$mailer = new MA_DBBackup_SqLite();

			$message = $mailer->sendBackup();

			$permissions->make_not_writable('audio');

			$events->write($user . ' made SQLite database backup.');
		}

		if ($_GET['do'] == 'clear_caches')
		{
			$clear->setFlag (array('SmartyCache', 'SmartyCompiledTemplates', 'PHCache', 'TimThumbCache', 'HTMLPurifierCache', 'Registry')); 
			$message = "caches_cleared";

		}

		if ($_GET['do'] == 'open_cache')
		{
			$cache_manager->open_cache_dirs();

			$message = 'caches_opened';

			$events->write($user . ' made cache directories world writeable.');
		}

		if ($_GET['do'] == 'close_cache')
		{
			$cache_manager->close_cache_dirs();

			$message = 'caches_closed';

			$events->write($user . ' restored normal permissions for cache directories.');
		}

		if ($_GET['do'] == 'delete_cookies')
		{
			$cooks = new cookieMaker();

			$cooks -> clearCookies();

			$events->write($user . ' deleted all cookies');

			$message = 'cookies_deleted';
		}

	} // close actions

} // close try block

catch (Exception $e)
{
	$message = $e->getMessage();
	$log->write($message);
	$warning = true;
}

// collect information to send to Smarty

// are we running PHP as cgi/fastcgi.....
if (strpos(PHP_SAPI, 'cgi') !== FALSE)
{
	$sapi = 'cgi';
}
else // ...or as an Apache module?
{
	$sapi = 'apache';
}

// compute information about sqlite
$sqlite_open = false;

if (file_exists($sqlite_db))
{
	$sqlite_open = (substr(sprintf('%o', fileperms(SQLITE_DIR)), -4) == '0777' 
					&& substr(sprintf('%o', fileperms($sqlite_db)), -4) == '0777'
					&& !file_exists($sqlite_htaccess));
}

// we use SMARTY_CACHE_DIR as a bell-weather for all cache dirs
$cache_state = CacheManager::get_permissions(SMARTY_CACHE_DIR);

// the most recent lines in the log files
$error_log = $log->getLastLines(10);
$events_log = $events->getLastLines(10);

$smarty->assign(array(	'ph_version' 				=> PH_VERSION,
						'db_type' 					=> DB_TYPE,
						'db_access' 				=> DB_CONNECTION_TYPE,
						'utilities_auth_key' 		=> $sess->createPageAuthenticator('utilities'),
						'system_function_disabled' 	=> is_disabled('system'),
						'xml_ok' 					=> can_read_remote_xml(),
						'windows'					=> (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'),
						'sapi' 						=> $sapi,
						'sqlite_open' 				=> $sqlite_open,
						'cache_state' 				=> $cache_state,
						'error_log'					=> $error_log,
						'events_log'				=> $events_log,
						'message'					=> $message,
						'warning' 					=> $warning
					));

?>
