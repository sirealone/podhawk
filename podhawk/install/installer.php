<?php

session_start();

// set error reporting
ini_set('display_errors', 1);

if (defined('E_DEPRECATED')) // ADODB_Lite throws some 'deprecated' errors
{
	error_reporting(E_ALL ^ E_DEPRECATED);
}
else
{
	error_reporting(E_ALL);
}

$actiontype = 'install';
require 'podhawk/inc/authority.php';

// define which version of PodHawk we are installing
	define ('THIS_PH_VERSION', '1.85');

// try to find a PodHawk config file
// load configuration class
require_once ('podhawk/inc/classes/Configuration.php');

$config = new Configuration();

$configured = $config->configure();

// if a valid config.php cannot be found, we need to define PATH_TO_ROOT
if (!$configured)
{
	define ('PATH_TO_ROOT', str_replace("\\", "/", getcwd()));
}

// are we attempting to install on a Windows system?
$windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

// define cache dirs etc
require (PATH_TO_ROOT . '/podhawk/common_definitions.php');

define ('PODHAWK_CONFIG', PATH_TO_ROOT . '/podhawk/custom/config.php');
define ('LOUDBLOG_CONFIG', PATH_TO_ROOT . '/loudblog/custom/config.php');
define ('FORCE_ADODB', false);
define ('DEBUG', 'log');
define ('PH_CACHING', false);

require "podhawk/inc/functions.php";
require 'podhawk/install/functions_install.php';

//print_r ($_SESSION);

// $status = 0 - not yet established that directories are writeable
// $status = 1 - directories are writeable
// $status = 2 - cache directories made, cache parents still writeable
// $status = 3 - cache parents returned to 0755 permissions, ready to get database details
// $status = 4 - cannot connect with given username, password etc
// $status = 5 - blank fields in form / can't find Loudblog config file
// $status = 6 - tables of an existing ph/lb database found
// $status = 7 - errors in writing to database (for debugging)
// $status = 8 - database ok, no new frontend user
// $status = 9 - database ok, new frontend user
// $status = 10 - database ok, new frontend and backend users (mysql root installs only)
// $status = 11 - user has not loaded the PodHawk config file properly
// $status = 12 - user has skipped ftp stage
// $status = 13 - error in ftp data
// $status = 14 - all complete
// $status = 15 - LoudBlog version is < 0.8


$page = (isset($_GET['page'])) ? $_GET['page'] : 0;

// if the 'status' session variable has not been set, start at the beginning
if (!isset($_SESSION['status']))
{
	$page = 0;
	$status = 0;
}
else
{
	$status = $_SESSION['status'];
}
// but if we are simply updating the database, and we have a valid config file, go straight to page 7.
if ($configured && isset($_POST['install_type']) && $_POST['install_type'] == 'update_database')
{
	$page = 7;
}

function __autoload($classname)
{
	__PHAutoload($classname);
}	

// these directories need to be writeable all the time
$operating_dirs = array('audio', 'images', 'upload');

// and these directories need to be writeable until we have created the cache directories
$cache_dir_parents = array('podhawk', 'podhawk/custom', 'podhawk/smarty', 'podhawk/timthumb');

// so altogether, these dirs need to be writeable
$writable_dirs = array_merge($operating_dirs, $cache_dir_parents);

// for calculating default values for form fields, we need a list of all fields
$all_form_fields = array("nickname", "login", "password", "sqltype", "sqlhost", "sqldata", "sqluser", "sqlpass", "siteurl", "email");

// for checking whether required fields, we need different lists for sqlite..
if (isset($_POST['sqltype']) && ($_POST['sqltype'] == 'sqlite' || $_POST['sqltype'] == 'sqlite3'))
{
	$form_fields = array('nickname', 'login', 'password', 'sqltype', 'siteurl', 'email');
}
else // ...and for other databases
{
	$form_fields = array("nickname", "login", "password", "sqltype", "sqlhost", "sqldata", "sqluser", "sqlpass", "siteurl", "email");
}

$ftp_fields = array('ftp_server', 'ftp_user', 'ftp_pass', 'ftp_path');

// find what database drivers are available
// driver_name => array(name in config file, display name)
if (extension_loaded('PDO') && FORCE_ADODB == false) // if we are using PDO (default)
{
	$databases = array(	'pdo_mysql' 	=> array('mysql', 'MySQL'),
						'pdo_pgsql' 	=> array('postgres8','PostgreSQL'),
						'pdo_sqlite' 	=> array('sqlite3', 'SQLite 3'));

}
else // we are using ADODB_Lite
{
	$databases = array(	'mysql' 	=> array('mysql', 'MySQL'),
						'pgsql' 	=> array('postgres8','PostgreSQL'),
						'sqlite' 	=> array('sqlite', 'SQLite 2'));
}

foreach ($databases as $d => $e)
{
	if (extension_loaded($d))
	{
		$supported_databases[$d] = $e;
	}
}

// which language are we using?
if (!isset($lang))
{
	if (isset($_SESSION['language']))
	{
		$lang = $_SESSION['language'];
	}
	else
	{
		$lang = 'english';
	}
}

include PATH_TO_ROOT . '/podhawk/install/lang/error_' . $lang . '.php';

$log = LO_EventLog::instance();

// code for individual pages - in reverse order to permit fallback to an earlier page in case of an error

#############################################
## Page 7 - checks that the ftp data is OK
## by attempting to connect using it.
## Displays final 'OK' message and sends an 
## email to the user.
############################################

if ($page == 7)
{
	$errLoc = "file " . __FILE__ . ' page 7';

	$error_string = "";
	
	try
	{
		$connect = new DB_Connection(DB_TYPE);
		define('DB_CONNECTION_TYPE', $connect->getConnectionType());
		$GLOBALS['lbdata'] = $connect->makeConnection();

		if (isset($_POST['submit_ftp']))  // if user has sent ftp data, test it
		{
			try
			{
				$conn = @ftp_connect($_POST['ftp_server']);

				if (!$conn)
				{
					$error_string = $e_message['cannot_connect'];

					throw new Exception('Unable to connect to FTP server.');
				}
				else
				{
					$login = @ftp_login($conn, $_POST['ftp_user'], $_POST['ftp_pass']);

					if (!$login)
					{
						$error_string = $e_message['cannot_login'];

						throw new Exception('Unable to login to FTP server');
					}
					else
					{				
						$path = @ftp_chdir($conn, $_POST['ftp_path']);

						if (!$path || substr($_POST['ftp_path'], -6) != 'upload')
						{
							$error_string = $e_message['cannot_find_upload'];
	
							throw new Exception ('Incorrect path from ftp root to upload folder');
						}
					}
				}
			} // close inner try block		
			catch (Exception $e)
			{						
				$page = 6;
				$status = 13;

				throw $e;
			}
			// encrypt the ftp information and place it in the database

			$blowfish = new SE_Blowfish;
			$_POST['ftp_user'] = $blowfish->encrypt($_POST['ftp_user']);
			$_POST['ftp_pass'] = $blowfish->encrypt($_POST['ftp_pass']);
			
			try
			{
				$GLOBALS['lbdata'] -> beginTransaction();
				
				$dosql = "UPDATE " . DB_PREFIX . "lb_settings SET value = :value WHERE name = :field";

				$GLOBALS['lbdata'] ->prepareStatement($dosql);		

				foreach ($ftp_fields as $field)
				{
					$input = array(':value' => $_POST[$field], ':field' => $field);
					$GLOBALS['lbdata']->executePreparedStatement($input);
				}

				$GLOBALS['lbdata'] -> commit();
			}
			catch (Exception $e)
			{
				$GLOBALS['lbdata'] -> rollBack();
				throw $e;
			}

			$status = 14;		

		} // close 'if there is FTP POST data'

		 // if we are re-installing or simply updating the database, run the autoupdate programmme
		elseif ((isset($_SESSION['install_type']) && $_SESSION['install_type'] == 're-install') || isset($_POST['install_type']) && $_POST['install_type'] == 'update_database')
		{
			$databaseUpdate = true;
			$dosql = "SELECT value FROM " . DB_PREFIX . "lb_settings WHERE name = 'ph_version'";
			$result = $GLOBALS['lbdata']->getArray($dosql);
			$version = $result[0]['value'];
			
			$autoupdate = new Autoupdate($version);

			$autoupdate->update();
			
			$updateMessage = $autoupdate->getMessage();
			if (empty($updateMessage)) $updateMessage = 'Your database is already up-to-date';

		}
		else
		{ 

			$status = 14;

		}

		// finally - if all complete - send email to user

		if ($status == 14)
		{
			if (!defined('MAIL_PATH'))
			{
				$p = ini_get('sendmail_path');
				$bits = explode(' ', trim($p));
				$ini_mail_path = $bits[0];
				$mail_path = (!empty($ini_mail_path)) ? $ini_mail_path : '/usr/sbin/sendmail';
				define ('MAIL_PATH', $mail_path);
			}

			$mailer = new MA_InstallNotifier();

			@$mailer->sendInstallMessage();

		} // close 'status == 14'

	} // close outer try block
	catch (Exception $e)
	{
		$log->error($e, $errLoc);
	}

} // close page 7

#####################################
## Page 6 - checks that user has correctly
## loaded the config file. Displays form
## for ftp data.
#####################################

if ($page == 6)
{
	// we have already included config.php if it exists - this tests that it has been included and that it isn't a dummy
	if (!defined('PAGE_AUTH_KEY') || PAGE_AUTH_KEY == "") // PAGE_AUTH_KEY is defined in the PodHawk config file, and nowhere else
	{
		$log->write('Unable to find valid PodHawk configuration file - install.php page 6'); 
		$page = 5;
		$status = 11;

	}
	else
	{			
		foreach ($ftp_fields as $field) // values for insertion in form	
		{
			$$field = (isset($_POST[$field])) ? $_POST[$field] : "";
		}
	}

}// end page 6


########################################
## Page 5 - this page does all the heavy 
## lifting of creating/converting the database;
## it also checks data integrity and computes and
## displays the new config file.
#######################################

#########################
#####  NEW INSTALL  #####
#########################

 // ensure that we do not recreate the database tables if user clicks reload button
if ($page == 5 && $status < 8 && $_SESSION['install_type'] == 'new_install')  
{

	$errLoc = 'file ' .  __FILE__ . ' page 5 new install';

	$form_complete = true;

	//start checking form data - some fields will not be submitted for sqlite
	if ($_POST['sqltype'] == "sqlite" || $_POST['sqltype'] == 'sqlite3')
	{
		$_POST['sqlhost'] = "";
		$_POST['sqldata'] = "";
		$_POST['sqluser'] = "";
		$_POST['sqlpass'] = "";
	}

	// check that all the form fields have been completed
	foreach ($form_fields as $field)
	{	
		if ($field == "siteurl" && $_POST['siteurl'] == "http://")
		{
			$form_complete = false;
		}
		elseif ($_POST[$field] == '')
		{
			$form_complete = false;
		}

	}

	if ($form_complete == false)
	{
		$log->write('Incomplete form data - install.php page 5');
		$page = 4;
		$status = 5;
	}
	else
	{
		$result = true;

		// attempt to connect to the database
		try
		{
			$connect = new DB_Connection($_POST['sqltype']);
			define('DB_CONNECTION_TYPE', $connect->getConnectionType());
			$GLOBALS['lbdata'] = $connect->makeConnection();
		
			// test if there is already a PodHawk/LoudBlog database
			$existing_database = check_existing_database($_POST['sqltype']);

			if ($existing_database)
			{
				$the_database = ($_POST['sqltype'] == 'sqlite' || $_POST['sqltype'] == 'sqlite3') ? 'loudblogdata.db' : $_POST['sqldata'];

				$result = false;

				throw new Exception ($e_message['existing_database_1'] . $the_database . $e_message['existing_database_2']);
			}			
		} // end try block

		catch (Exception $e)
		{
			// error message on page 4 if a connection cannot be made
			$log->error($e, $errLoc);
			$error_message = $e->getMessage();
			$page = 4;
			$status = 4;
			$result = false;
		}
			
		if ($result)
		{
			if (!defined('DB_PREFIX'))
			{
				// generate a random database prefix
				$prefix = strtolower(generatePassword(5));
				// Postgres table names cannot begin with a number
				if (is_numeric(substr($prefix, 0, 1)))
				{
					$prefix = "a" . substr($prefix, 1);
				}
				define ('DB_PREFIX', $prefix . "_");
			}

			require (PATH_TO_ROOT . '/podhawk/install/database.php');

			// write data to database
			$type = $_POST['sqltype'];
			if ($type == 'postgres8' || $type == 'postgres7')
			{
				$type = 'postgres';
			}

			$problem = false;
			$error_message = $e_message['db_problem'];
	
			// create the tables
			foreach ($tables_to_make[$type] as $dosql)
			{

				try
				{
					$result = $GLOBALS['lbdata']->Execute($dosql);
				}

				catch (exception $e)
				{
					$log->error($e, $errLoc . ' error while creating tables');
					$error_message .= $e->getMessage() . "<br />";
					$problem = true;		
				}
			}

			//populate some of the tables with data
			foreach ($insert_array as $table => $dosql)
			{
				try
				{
					$GLOBALS['lbdata']->prepareStatement($dosql);
					$GLOBALS['lbdata']->executePreparedStatement($insert_data_prepared_statement_array[$table]);
				}

				catch (exception $e)
				{
					$log->error($e, $errLoc . ' error while inserting table data');
					$error_message .= $e->getMessage(). "<br />";
					$problem = true;
				}
			}

			//enter 'name/value' data in the settings and players tables					
			foreach ($name_value_pairs as $table => $data)
			{
				try
				{
					$GLOBALS['lbdata'] -> beginTransaction();

					$dosql = "INSERT INTO " . DB_PREFIX . "lb_$table ({$data['col1']}, {$data['col2']}) VALUES (:name, :value)";

					$GLOBALS['lbdata'] -> prepareStatement($dosql);
				
					foreach ($data['data'] as $a => $b)
					{
						$insertArray = array(':name' => $a, ':value' => $b);
						$GLOBALS['lbdata'] -> executePreparedStatement($insertArray);
					}
					
					$GLOBALS['lbdata']->commit();
				}
				catch (exception $e)
				{
					$GLOBALS['lbdata'] -> rollBack();
					$log->error($e, $errLoc . ' error while inserting name/value data');
					$error_message .= $e->getMessage() . '<br />';
					$problem = true;
				}			
			}

		//if we detect that we have not been able to run some of the database commands, abort the install process
		if ($problem)
		{
			$status = 7;
			$page = 4;

			// and drop the tables, to leave an empty database
			foreach ($all_tables as $table)
			{
				$dosql = $drop_table[$type] . DB_PREFIX . "lb_" . $table;

				try
				{
					$GLOBALS['lbdata']->Execute($dosql);
					$log->write('Database errors detected. All tables deleted to permit clean restart.');
				}

				catch (exception $e)
				{
					$log->error($e, $errLoc);
				}
			}
			
		}
		else
		{ // if there have been no problems in creating the database

			$status = 8;		

			//try to create new front-end user
			$fe_password = generatePassword(8);
			$fe_user = "fe_user_".mt_rand(100,999);

			$dosql_array = create_user($type, $fe_user, $fe_password, $_POST['sqldata'], $_POST['sqlhost']);

			foreach ($dosql_array as $dosql) // for sqlite, $dosql_array is an empty array
			{
				try
				{
					$result = $GLOBALS['lbdata']->Execute($dosql);
				}

				catch (exception $e)
				{
					$fe_user = "";
					$fe_password = "";
					$log->write('Unable to create front-end database user');
				}

				if ($result)
				{
					$status = 9;
					$log->write('Created front-end database user.');
				} 
			}

			//if installation has been created by mysql root user, create a new back-end user
			if ($type == "mysql" && $_POST['sqluser'] == "root")
			{
				$backend_user = "user_".generatePassword(4);
				$backend_password = generatePassword(8);

				$dosql = create_backend_user($backend_user, $backend_password, $_POST['sqldata'], $_POST['sqlhost']);

				try
				{
					$result = $GLOBALS['lbdata']->Execute($dosql);
				}
				catch (exception $e)
				{
					$log->error($e, $errLoc . ' while creating new database user.');
				}

				if ($result)
				{
					$status = 10;
					$_POST['sqluser'] = $backend_user;
					$_POST['sqlpass'] = $backend_password;
					$log->write('Created new backend database user.');
				}				
			}

			//prepare sqlite
			if ($type == 'sqlite' || $type == 'sqlite3')
			{ 
				$fe_user = "";
				$fe_password = "";
			
				// no-one except the web server can read or write to the database
				chmod (SQLITE_DIR . 'loudblogdata.db', 0600); 

			}

			//the configuration file
			$prefix = DB_PREFIX;
			$root = PATH_TO_ROOT;
			$key = generatePassword(8);
			$blowfish = generatePassword(16);
			$config_file = <<<EOF
<?php
// YOUR DATABASE INFORMATION --------------------
define('DB_TYPE', '{$_POST['sqltype']}');
define('DB_HOST', '{$_POST['sqlhost']}');
define('DB_NAME', '{$_POST['sqldata']}');
define('DB_USER', '{$_POST['sqluser']}');
define('DB_PASS', '{$_POST['sqlpass']}');
define('DB_FE_USER', '$fe_user');
define('DB_FE_PASS', '$fe_password');
define('DB_PREFIX', '$prefix'); 

// DOCUMENT ROOT ---------------------
define('PATH_TO_ROOT', '$root');

//authorisation key for admin page forms
define ('PAGE_AUTH_KEY', '$key');

//blowfish encryption key
define('BLOWFISH_KEY', '$blowfish');
?>
EOF;

			//store the config file in a session variable, so that it is not lost if user presses reload button
			$_SESSION['config_file'] = $config_file;

			} // close 'if we have created database tables and written data into them'

		} // close 'if we have been able to connect to the database'

	} // close 'if form is complete'

} // close page 5 / new_install

############################
##### Convert LoudBlog #####
############################

elseif ($page == 5 && $status < 8 && $_SESSION['install_type'] == 'convert_loudblog')
{
	$errLoc = 'file ' . __FILE__ . ' page 5 Loudblog conversion';
	
	//load the loudblog configuration file
	if (file_exists(LOUDBLOG_CONFIG))
	{
		include (LOUDBLOG_CONFIG);
	}

	if (empty($db['type']))
	{
		$status = 5;
		$page = 4;
		$log->write('Unable to find valid LoudBlog configuration file.');
	}
	else
	{
		try
		{	
			// we attempt to connect using the info in the loudblog config file
			$connect = new DB_Connection($db['type']);
			define('DB_CONNECTION_TYPE', $connect->getConnectionType());
			$GLOBALS['lbdata'] = $connect->makeConnection();
			$result = true;
		}
		catch (exception $e)
		{
			$status = 4;
			$page = 4;
			$error_message = $e->getMessage();
			$log->error($e, $errLoc . ' while attempting db connection.');
			$result = false;
		}

		if ($result) //we can connect to the database 
		{ 
			define ('DB_PREFIX', $db['pref']);

			require (PATH_TO_ROOT . '/podhawk/install/database.php');

			// convert the LoudBlog 0.8 database to PodHawk
			$success = convert_database($db['type']);

			if ($success[0] == false) // if some database operations have failed
			{
				$status = 7;
				$page = 4;
				$error_message = $success[1];
				$log->write($error_message);

			}
			else
			{ // if database operations have succeeded	

				$status = 8;		

				//try to create new front-end user
				$fe_password = generatePassword(8);
				$fe_user = "fe_user_".mt_rand(100,999);

				$dosql_array = create_user($db['type'], $fe_user, $fe_password, $db['data'], $db['host']);

				foreach ($dosql_array as $dosql)
				{
					try
					{
						$result = $GLOBALS['lbdata']->Execute($dosql);
					}

					catch (exception $e)
					{
						$fe_user = "";
						$fe_password = "";
						$log->write('Unable to create front-end database user.');
					}

					if ($result)
					{
						$status = 9;
						$log->write('Created fron-end database user.');
					} 
				}

				//if installation has been created by mysql root user, create a new back-end user
				if ($db['type'] == "mysql" && $db['user'] == "root")
				{
					$backend_user = "user_".generatePassword(4);
					$backend_password = generatePassword(8);

					$dosql = create_backend_user($backend_user, $backend_password, $db['data'], $db['host']);
					try
					{
						$result = $GLOBALS['lbdata']->Execute($dosql);
					}

					catch (exception $e)
					{
						$log->error($e, $errLoc . ' while attempting to create backend db user.');
						}

					if ($result) {
						$status = 10;
						$db['user'] = $backend_user;
						$db['pass'] = $backend_password;
						$log->write("Created new db backend user $backend_user");						
					}
				}

				// move the iTunes image etc to the images folder
				move_images();

				//if sqlite, move the database to the new 'audio/sqlite' folder
				if ($db['type'] == 'sqlite')
				{
					rename (PATH_TO_ROOT . '/audio/loudblogdata.db', SQLITE_DIR . 'loudblogdata.db');
				}
			
				$prefix = DB_PREFIX;
				$root = PATH_TO_ROOT;
				$key = generatePassword(8);
				$blowfish = generatePassword(16);
				$config_file = <<<EOF
<?php
// YOUR DATABASE INFORMATION --------------------
define('DB_TYPE', '{$db['type']}');
define('DB_HOST', '{$db['host']}');
define('DB_NAME', '{$db['data']}');
define('DB_USER', '{$db['user']}');
define('DB_PASS', '{$db['pass']}');
define('DB_FE_USER', '$fe_user');
define('DB_FE_PASS', '$fe_password');
define('DB_PREFIX', '$prefix'); 

// DOCUMENT ROOT ---------------------
define('PATH_TO_ROOT', '$root');

//authorisation key for admin page forms
define ('PAGE_AUTH_KEY', '$key');

//blowfish encryption key
define('BLOWFISH_KEY', '$blowfish');
?>
EOF;


				$_SESSION['config_file'] = $config_file;

			} // end 'if no database errors'

		} // end if we can connect to the database

	} //end valid loudblog config file exists

}// end page 5 - convert Loudblog

###################################
## Page 4 - display the form
## for database information
##################################

if ($page == 4)
{
	
	foreach ($all_form_fields as $field)
	{
		$$field = (isset($_POST[$field])) ? $_POST[$field] : "";
		if ($field == "siteurl" && !isset($_POST['siteurl'])) $siteurl = 'http://';
		if ($field == 'sqlhost' && !isset($_POST['sqlhost'])) $sqlhost = 'localhost';
	}

	$selected_mysql = "";
	$selected_sqlite = "";
	$selected_postgres8 = "";

	if (isset($_POST['sqltype']))
	{
		switch ($_POST['sqltype'])
		{
			case "mysql":
				$selected_mysql = " selected=\"selected\"";
			break;

			case "sqlite":
			case "sqlite3":
				$selected_sqlite = " selected=\"selected\"";
			break;

			case "postgres8":
				$selected_postgres8 = " selected=\"selected\"";
			break;

			default :
				$selected_mysql = " selected=\"selected\"";
		}
	}
	else
	{

		$selected_mysql = " selected=\"selected\"";

	}

}

##########################################
## Page 3 - create the cache directories,
## ask user to return cache parent directories to 0755
##########################################

if ($page == 3)
{
	$revert_perms_ok = true;

	if ($status == 0)
	{
		$page = 2;
	}
	elseif (!$windows) // we only revert permissions for *nix systems
	{
		foreach ($cache_dir_parents as $dir)
		{
			$path = PATH_TO_ROOT . "/" . $dir;
			$p = substr(sprintf('%o', fileperms($path)), -4);
			if ($p != "0755") $revert_perms_ok = false;
			$permissions[$dir]['permissions'] = $p;
			$permissions[$dir]['class'] = ($p == '0755') ? 'green' : 'red';
		}
		
	}	

	if ($status == 1)
	{			
		$cache = new CacheManager($cache_dirs);

		$cache->make_directory_all();

		$status = 2;
	}

	if ($revert_perms_ok == true)
	{
		$status = 3;
	}

	// test that the cache directories have actually been created - return to page 2 if not	
	$cache_dirs_exist = true;

	foreach ($cache_dirs as $dir)
	{
		if (!is_writable($dir)) $cache_dirs_exist = false;
	}

	//.. and that the operating directories are writeable
	foreach ($operating_dirs as $dir)
	{
		if (!is_writable(PATH_TO_ROOT . '/' . $dir)) $cache_dirs_exist = false;
	}

	// if not, it's back to page 2 to put things right!
	if ($cache_dirs_exist == false)
	{
		$status = 0;
		$page = 2;		
	}			

}

############################################
## Page 2 - make sure that the operating
## directories and the parent directories
## of the cache directories are writable
###############################################

if ($page == 2)
{

	$writable = array();
	$perms_ok = true;
	
	foreach ($writable_dirs as $dir)
	{
		if (is_writable(PATH_TO_ROOT . '/' . $dir))
		{
			$writable[$dir]['writable'] = 'Writable';
			$writable[$dir]['class'] = "green";
		}
		else
		{
			$writable[$dir]['writable'] = 'Not writable';
			$writable[$dir]['class'] = 'red';
			$perms_ok = false;
		}
	}

	$status = ($perms_ok) ? 1 : 0;

	// if the cache dirs have already been created...
	$cache_dirs_exist = true;

	foreach ($cache_dirs as $dir)
	{
		if (!is_writable($dir)) $cache_dirs_exist = false;
	}

	//.. and the operating directories are writeable..

	foreach ($operating_dirs as $dir)
	{
		if (!is_writable(PATH_TO_ROOT . '/' . $dir)) $cache_dirs_exist = false;
	}
	//.. then we can skip a stage
	if ($cache_dirs_exist == true) $status = 2;	

}

###################################
## Page 1 - explain the install
## process
####################################
if ($page == 1)
{

	$lang = (isset($_POST['language'])) ? $_POST['language'] : "english";
	$_SESSION['language'] = $lang;
	$install_type = (isset($_POST['install_type'])) ? $_POST['install_type'] : "new_install";
	$_SESSION['install_type'] = $install_type;

	if ($install_type == 'convert_loudblog')
	{
		try
		{	
	
			if (!file_exists(LOUDBLOG_CONFIG))
			{
				throw new Exception ($e_message['no_loudblog_config']);
			}

			require LOUDBLOG_CONFIG;

			$connect = new DB_Connection($db['type']);
			
			$GLOBALS['lbdata'] = $connect->makeConnection();

			$dosql = "SELECT * FROM " . $db['prefix'] . "lb_settings";
			$settings = $GLOBALS['lbdata']->GetAssoc($dosql);

			if (!isset($settings['version080']))
			{		
				$lb_version = $e_message['pre_lb_6'];
				if (isset($settings['version06'])) $lb_version = 'LoudBlog 0.6';
				if (isset($settings['version07'])) $lb_version = 'LoudBlog 0.7';
				if (isset($settings['version071'])) $lb_version = 'LoudBlog 0.71';

				$lb_version = $e_message['lb8_only'] . $lb_version . $e_message['lb8_only_2'];

				throw new Exception ($lb_version); 
			}

		}  // end try

		catch (Exception $e)
		{
			$page = 0;
			$status = 15;
			$error_message = $e->getMessage();
		}

	} // close 'convert loudblog'
} // close page == 1

#####################################
## Page 0 - choose a language, and say 
## whether you want a new install 
## or a LoudBlog upgrade
###################################### 
if ($page == 0)
{
	//if we can find a loudblog config file, assume that the user wants to upgrade from LoudBlog
	if (file_exists(LOUDBLOG_CONFIG))
	{		
		$checked1 = '';
		$checked2 = " checked=\"checked\"";
		$checked3 = '';
		
	}
	else // assume we are making a new PodHawk install
	{
		$checked1 = " checked=\"checked\"";
		$checked2 = '';
		$checked3 = '';
	}
	// NB there is no foolproof way of testing whether user wants to upgrade an existing PH site as opposed to installing a new one

	$lang_file_array = get_dir_contents (PATH_TO_ROOT . "/podhawk/install/lang");

	foreach($lang_file_array as $lang_file)
	{
		if (substr($lang_file, -3) != 'php') continue;
		if (substr($lang_file, 0, 6) == 'error_') continue;
		$bits = explode('.', $lang_file);
		$languages[] = $bits[0];
	}

	if ($status != 15) $status = 0;

}
			
// set the status session variable
$_SESSION['status'] = $status;


include PATH_TO_ROOT . '/podhawk/install/lang/' . $lang . '.php';

// display the appropriate webpage
require "podhawk/install/pages.php";

echo $head;

echo $body;

echo $footer;
 
?>
