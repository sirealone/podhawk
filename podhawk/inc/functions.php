<?php 
// ----------------------------------------------------- //
// PodHawk                                               //
// easy-to-use audioblogging and podcasting              //
//                                                       // 
// http://www.podhawk.com                               //
//                                                       //
// Based on the fabulous 'LoudBlog'                      //
// by Gerrit van Aaken and Sebastian Stein               //
// Modified and extended by Peter Carter                 //
// Released under the Gnu General Public License         //
// http://www.gnu.org/copyleft/gpl.html                  //
//                                                       //
// Have Fun! Drop me a line if you like PodHawk!        //
// ----------------------------------------------------- //
####################################################
#
# Functions connected to security
#
# whitelist
# cleanmygets
# stripslashes_deep
# killevilcharacters
# authority
# allowed_characters
# escape
# check_update
# generatePassword
#
#################################################
#################################################

# we put all our whitelists in one place so that we can update them easily

function whitelist($type)
{
	$reg = Registry::instance();

	switch ($type)
	{

	case 'backend_pages' :
		$return = array('ajax', 'all_id3_info', 'authors1', 'authors2', 'cats', 'comments', 'credits', 'find', 'game', 'id3','images', 'info', 'javaload', 'login', 'players', 'postings', 'record1', 'record2', 'settings', 'spam', 'stats', 'updateUploadFolder', 'autocomplete', 'ping', 'playlist', 'plugins', 'testFTP', 'autosave', 'utilities', 'imagelist', 'addfiles');
	break;

	case 'backend_languages' :
		$lang_files = get_dir_contents('lang');
		foreach ($lang_files as $file)
		{
			$bits = explode('.', $file);
			if (!isset($bits[1]) || $bits[1] != 'php') continue;
			$langs[] = $bits[0];
		}
		return $langs;
		break;

	case 'date' :
		$return = "/^[0-9]{4}\-[0-9]{2}(\-[0-9]{2})?$/";
		break;

	case 'cats' :
		$return = $reg->getCategoryNames();

		$return = array_values($return); // index array numerically, not by category id

		//we may have stripped spaces from the category name,
		//so we add space-removed versions of relevant cat names to the array
		foreach ($return as $cat)
		{
			if (strpos($cat, ' '))
			{
				$return[] = str_replace(' ','',$cat);
			}
		}
		break;

	case 'tags' :
		$tag = TagManager::instance();

		$return = $tag->getAllTagsList();
		break;

	case 'themes' :
		$return = get_dir_contents('podhawk/custom/themes');
		break;

	case 'atp' :
		$return = "/^[a-z0-9_]+$/";
		break;

	case 'bool' :
		$return = array ('1', '0');
		break;

	case 'email' :
		$return = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
		break;

	case 'web' :
		$return = "/^[0-9a-z.:\/_+-]+$/i";		
		break;

	case 'author_names' :
		$return = $reg->getAuthorNicknames();
		break;

	default :
		$return = array();
		break;
	}

return $return;

}
 

#################################################
#################################################


function cleanmygets() {

$status = "";

	//remove all dodgy characters
	foreach ($_GET as $name => $value)
	{
		$_GET[$name] = killevilcharacters($value);
	}
		

	//id must contain only numeric characters
    if (isset($_GET['id']))
	{
    	if(!ctype_digit($_GET['id']))
		{
			$status = "Illegal value '{$_GET['id']}' for GET id";
			unset($_GET['id']);
		}
    }
    
    //page must contain only numeric characters
    if (isset($_GET['page'])) {
    	if(!ctype_digit($_GET['page']))
		{			
			$status = "Illegal value '{$_GET['page']}' for GET page";
			unset($_GET['page']);
		}
    }
    
    //atp (alternative template) must contain only a-z (lower case), 0-9 or _
    if (isset($_GET['atp']))
	{
    	if(!preg_match(whitelist('atp'),$_GET['atp']))
		{		
			$status = "Illegal value '{$_GET['atp']}' for GET atp";
			unset($_GET['atp']);
		}
    }
    //com must be either 1 or 0
    if ((isset($_GET['com'])) && (!in_array($_GET['com'], whitelist('bool'))))
	{		
		$status = "Illegal value '{$_GET['com']}' for GET com";
		$_GET['com'] = "0";
    }
    
    //date must be of the form YYYY-MM(-DD)
    if (isset($_GET['date']))
	{
    	if (!preg_match(whitelist('date'),$_GET['date']))
		{			
			$status = "Illegal value '{$_GET['date']}' for GET date";
			unset($_GET['date']);
		}
	}
    //an instruction to the calendar must have the form YYYY-MM(-DD)
    if (isset($_GET['cal']))
	{
    	if (!preg_match(whitelist('date'),$_GET['cal']))
		{		
			$status = "Illegal value for '{$_GET['cal']}' for GET cal";
			unset($_GET['cal']);
		}
	}
   
    //cat must match a real category name
    if (isset($_GET['cat']))
	{
    	$request = entity_encode($_GET['cat']);

		if (!in_array($request,whitelist('cats')))
		{			
			$status = "Illegal value '{$_GET['cat']}' found for GET cat";
			unset ($_GET['cat']);
		}
    }

	//author must be a number (=author_id)
    if (isset($_GET['author']))
	{
	 	if(!ctype_digit($_GET['author']))
		{		
			$status = "Illegal value '{$_GET['author']}' for GET author";
			unset ($_GET['author']);  
			}
	}

	//tag must match a real tag name
    if (isset($_GET['tag']))
	{
    	if (!in_array(entity_encode($_GET['tag']), whitelist('tags')))
		{			
			$status = "Illegal value '{$_GET['tag']}' for GET tag";
			unset ($_GET['tag']);
    	}
	}

	//preview must be either 1 or 0
    if ((isset($_GET['preview'])) && (!in_array($_GET['preview'], whitelist('bool'))))
	{		
		$status = "Illegal value '{$_GET['preview']}' for GET preview";
		unset ($_GET['preview']);
    }

	//theme must be in the themes directory
    if (isset($_get['theme']))
	{
		if (!in_array($_GET['theme'], whitelist('themes')))
		{
		$status = "Illegal value '{$_GET['theme']}' for GET theme";
		unset ($_GET['theme']);
		}
	}
return $status;
}


#################################################
#################################################
# a recursive version of stripslashes

function stripslashes_deep($value)
{
    $output = (is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value));
    return $output;
}

#################################################
#################################################

function killevilcharacters($text)
{
    $trans = array(
		" "		=> '',
		".." 	=> '',
		"/"  	=> '',
		"'" 	=> '',
		'"'  	=> '',
		'"'  	=> '',
		'<'  	=> '',
		'>'  	=> '',
		'\\' 	=> "",
		'--'	=> "",
		';' 	=> "",
		'*' 	=> "",
		'xp_' 	=> "");

    return strtr($text, $trans);
}

######################################################

function allowed_characters($string, $type)
{
	//checks that e-mail addresses and web addresses in 
	//submitted POST data contain no dangerous characters
	$regex = whitelist($type);
	$return =  (!preg_match($regex, $string)) ? "" : $string;
	return $return;
}

######################################################


function escape($string, $quotes=true)  {

$string = $GLOBALS['lbdata']->qstr($string);

if (!$quotes) {
	$string = substr($string, 1, -1);
	}

return $string;

}


########################################################

function generatePassword ($length)
{

	$password = "";
	$possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
	$i = 0; 
		
	while ($i < $length)
	{ 

	   	$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		$password .= $char;
	   	$i++;    

	 }

	return $password;

}

#################################################
#################################################
#
# General utility functions
#
# veryempty
# addToUrl
# get_dir_contents
#
#################################################

/**
 * extended version of empty()
 *
 */
function veryempty ($text)
{
    $output = ((empty($text)) || (trim($text) == '') || ($text == 'NULL') || ($text == '0')) ? true: false;
    return $output;
}

#################################################

/**
 * returns a string with the attributes of the current URL plus the given one
 *
 */
function addToUrl ($att, $value)
{
    $return = "";

    foreach ($_GET as $oldatt => $oldvalue)
	{
    	if ($oldatt != $att)
		{
    		$return .= "&amp;".$oldatt."=".urlencode($oldvalue);
    	}
    }

    $return .= "&amp;".$att."=".urlencode($value);

    return "?".substr($return, 5);
}

###############################################
###############################################
# returns an array containing a list of the contents of $dir
function get_dir_contents ($dir)
{
	$f = opendir($dir); 
	
	$contents = array();
	while (($file = readdir($f)) !== FALSE)
	{ 
		if ( substr($file, 0, 1) != ".") // ignore hidden files and . and ..
		{ 
		    $contents[] = $file;			       
		}
	}
	closedir($f);
	return $contents;
}

################################################
# Tests whether $function_name has been disabled in php.ini

function is_disabled($function_name)
{
	$disabled_functions = explode(',', ini_get('disable_functions'));

	return in_array($function_name, $disabled_functions);
}

#################################################
# Tests whether podHawk can check for updates (using check_updates() function above)

function can_read_remote_xml()
{
	return (function_exists('simplexml_load_file') && (function_exists('curl_setops') || ini_get('allow_url_fopen') == true));
	
}

#################################################
#################################################

#
# Functions for the manipulation of media files
#
# 
# 
# 
# uploadlimit
#
# 
#################################################

#################################################
#################################################

/**
 * calculates the upload-via-browser size limit
 *
 * @return unknown
 */
function uploadlimit()
{
    $load = ini_get('upload_max_filesize');
    $post = ini_get('post_max_size');

    $load = trim($load);
    $last = strtolower($load{strlen($load)-1});

    switch($last)
	{
		case 'g': $load *= 1024;
		case 'm': $load *= 1024;
		case 'k': $load *= 1024;
    }

    $post = trim($post);
    $last = strtolower($post{strlen($post)-1});
    switch($last)
	{
		case 'g': $post *= 1024;
		case 'm': $post *= 1024;
		case 'k': $post *= 1024;
    }

    if ($post <= $load)
	{
		return $post;
	}
	else
	{
		return $load;
	}
}

#################################################
// returns the extension from a file name
// returns extension (without leading '.') or false if no '.' is found in the filenemae
function getExtension($filename)
{
	$f = strtolower($filename);
	$ext = substr(strrchr($f, '.'), 1);
	return $ext;
}

###########################################################

//reads the number of downloads of an audio file
// NB Smarty templates will look for a function of this name - do not change it!

function insert_downloads($param)
{
	
	$dosql = "SELECT countall FROM " . DB_PREFIX . "lb_postings WHERE id = :id";
	$GLOBALS['lbdata']->prepareStatement($dosql);
	$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $param['id']));
	
	return $result[0]['countall'];
}

##################################################################
// reads number of visitors
// NB Smarty templates will look for function of this name

function insert_visitors($param)
{
	$seconds = $param['seconds'];
	
	$v = US_Visitor::instance();

	return $v->getVisitors(3600);
}

#################################################################
// creates dynamic block
// NB Smarty templates will look for function of this name

function smarty_block_dynamic($param, $content, &$smarty)
{
    return $content;
}


################
#
# Functions for encoding and decoding
#
# unichr
# 
# entities_to_characters
# change_entities
# 
#
# entity_encode
# my_html_entity_decode
# html_to_xml
#
#################################################
#################################################
// keep
function unichr($dec) {
    if ($dec < 256) {
        $utf = chr($dec);
    } elseif ($dec < 2048) {
        $utf = chr(192 + (($dec - ($dec % 64)) / 64));
        $utf .= chr(128 + ($dec % 64));
    } else {
        $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
        $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
        $utf .= chr(128 + ($dec % 64));
    }
    return $utf;
}

#################################################
#################################################
// keep
function entities_to_chars($text)
{
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);

    //some HTML4.0 Entities
    $trans_tbl['&#039;']   = "'";
    $trans_tbl['&#8220;']  = "\"";
    $trans_tbl['&#8221;']  = "\"";
    $trans_tbl['&#8222;']  = "\"";
    $trans_tbl['&#8249;']  = "'";
    $trans_tbl['&#8250;']  = "'";
    $trans_tbl['&#8216;']  = "'";
    $trans_tbl['&#8217;']  = "'";
    $trans_tbl['&#8218;']  = "'";
    $trans_tbl['&#8211;']  = "-";
    $trans_tbl['&#8212;']  = "-";
    $trans_tbl['&mdash;']  = "-";
    $trans_tbl['&ndash;']  = "-";
    $trans_tbl['&euro;']   = "EUR";
    $trans_tbl['&#8364;']  = "EUR";
    $trans_tbl['&apos;']   = "'";
    $trans_tbl['&#8217;']  = "'";
    $trans_tbl['&hellip;'] = "...";
    $trans_tbl['&#8230;']  = "...";
    $trans_tbl['&#8240;']  = "%%";

    $ret = strtr($text, $trans_tbl);
    $ret = preg_replace("/&#(\d{2,5});/e", "unichr($1);", $ret);
    //$ret = preg_replace('/&#(\d+);/me', "chr('\\1')",$ret);
    $ret = preg_replace('|&\w*;|me', "",$ret);
    return $ret;
}

##############################################
##############################################
// we want to save stuff to the database in a form which can be sent to a browser,
// so we need to encode html special characters, but avoid double encoding.
// Unfortunately, the 'double_encode' parameter in htmlspecialchars is
// available only from php 5.2.3

function entity_encode($string)
{
	// decode all html entities (so that we don't double encode '&' in encoded entities)
	$string = my_html_entity_decode($string); 

	$string = htmlspecialchars($string, ENT_QUOTES, "UTF-8"); // encode special characters
	
	return $string;
}

#############################################
#############################################

//a convenient way of decoding html entities

function my_html_entity_decode($string)
{

	$string = html_entity_decode($string, ENT_QUOTES, "UTF-8");

	return $string;
}

############################################
# tests whether the jw player is installed in the appropriate location

function jwplayer_installed ()
{

	return (file_exists(JW_DIR."player.swf") && file_exists(JW_DIR . "jwplayer.js"));

}

################################################
// a function to change the value of a specified variable using values in an array returned by a plugin,
// or to change a property of an object
// it will cope with up to 3 levels of variable offsets
function rewriteVariables($j)
{
	if (isset($j['variable']))
	{	
		global ${$j['variable']};
		$variable = $j['variable'];
		if (empty($j['offset']))
		{
			$$variable = $j['value'];
		}
		else
		{
			$c = count($j['offset']);

			switch ($c)
			{
				case 1 :
				$offset_1 = $j['offset'][0];
				${$variable}[$offset_1] = $j['value'];
				break;

				case 2 :
				$offset_1 = $j['offset'][0];
				$offset_2 = $j['offset'][1];
				${$variable}[$offset_1][$offset_2] = $j['value'];
				break;

				case 3 :
				$offset_1 = $j['offset'][0];
				$offset_2 = $j['offset'][1];
				$offset_3 = $j['offset'][2];
				${$variable}[$offset_1][$offset_2][$offset_3] = $j['value'];
				break;
			}
		}
	}
	elseif (isset($j['object']))
	{
		$objectName = $j['object'];

		global $$objectName;

		if (isset($j['method'])) // change a property of an object by calling a 'setter' method
		{
			$methodName = $j['method'];

			$value = $j['value'];
	
			$$objectName->$methodName($value);
		}
		elseif (isset($j['property'])) // else change the value of the property directly
		{
			$property = $j['property'];
	
			$$objectName->$property = $j['value'];
		}
	}
		
}

################################################################
//this little baby will autoload class files for us
function __PHAutoload($classname)
{
	$prefix = substr($classname, 0, 3);

	if (file_exists(INCLUDE_FILES . "/" . $prefix . "classes/" . $classname . ".php"))
	{
		include (INCLUDE_FILES . "/" . $prefix ."classes/" . $classname . ".php");
		return TRUE;
	}
	elseif (file_exists(INCLUDE_FILES . "/classes/" . $classname . ".php"))
	{
		include (INCLUDE_FILES . "/classes/" . $classname . ".php");
		return TRUE;
	}
	elseif (substr($classname, 0, 8) == 'Plugin__') // eg Plugin__plugin_name__myclass
	{
		$bits = explode('__', $classname);
		$plugin = strtolower($bits[1]);
		if (file_exists(PLUGINS_DIR . "$plugin/$classname.php"))
		{
			include (PLUGINS_DIR . "$plugin/$classname.php");
			return true;
		}
		else
		{
			return false;
		}
	}
	else return FALSE;
}

###########################################
## $root = an absolute file path
## $dir = a path relative to $root
## returns absolute file path for $dir
###########################################

function resolveDir($dir, $root)
{

	while (substr($dir, 0, 3) == '../')
	{		
		$dir = substr($dir, 3); // remove '../'
		$root = dirname($root); // go up one level			
	}
		
	if (substr($root, -1) == '/') // remove a possible trailing slash at the end of $root
	{
		$root = substr($root, 0, -1);
	}

	$path = $root . DIRECTORY_SEPARATOR . $dir;

	if (substr($path, -1) != DIRECTORY_SEPARATOR) // if there is no terminating slash, add one
	{
		$path = $path . DIRECTORY_SEPARATOR;
	}

	return $path;
		
}
#############################################################
#### Simulations for functions which may not be present
#### in some php versions
#############################################################

	// we need to simulate json_encode() and json_decode() where these functions do not exist
	if (!function_exists('json_encode'))
	{
		require PATH_TO_ROOT . "/podhawk/lib/JSON.php";
	}

	// similarly we need to simulate ctype_digit() where the ctype extension is not loaded
	if (!function_exists('ctype_digit'))
	{
		function ctype_digit($var)
		{
			$regex = "/^[0-9]+$/";
			return preg_match($regex, $var);
		}
	}

	// similarly we need a work-around for curl_setopt_array() in php < 5.1.3
	if (!function_exists('curl_setopt_array'))
	{
   		function curl_setopt_array(&$ch, $curl_options)
   		{
      		foreach ($curl_options as $option => $value)
			{
           		if (!curl_setopt($ch, $option, $value))
				{
               		return false;
           		} 
       		}
       		return true;
   		}
	}

?>
