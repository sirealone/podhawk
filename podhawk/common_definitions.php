<?php

// definitions, chiefly of cache file locations, which are used both by the install programme
// and by the main PodHawk programme

define ('INCLUDE_FILES', PATH_TO_ROOT . '/podhawk/inc');

define ('SQLITE_DIR', PATH_TO_ROOT . '/podhawk/custom/sqlite/');
define ('SMARTY_CACHE_DIR', PATH_TO_ROOT . '/podhawk/smarty/cache/');
define ('LOG_DIR', PATH_TO_ROOT . '/podhawk/custom/log/');
define ('SMARTY_COMPILED_TEMPLATES_CACHE', PATH_TO_ROOT . '/podhawk/smarty/templates_c/');
define ('TIMTHUMB_CACHE', PATH_TO_ROOT . '/podhawk/timthumb/cache/');
define ('TIMTHUMB_TEMP_DIR', PATH_TO_ROOT . '/podhawk/timthumb/temp/');
define ('PH_CACHE', PATH_TO_ROOT . '/podhawk/ph_cache/');
define ('HTML_PURIFIER_CACHE', PATH_TO_ROOT . '/podhawk/html_purifier_cache/');

//define PHP_VERSION_ID (not defined before PHP 5.2.7)
if (!defined('PHP_VERSION_ID'))
{
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

// array of cache directories which are to be owned if possible by the Podhawk programme

$cache_dirs = array(SMARTY_CACHE_DIR,
					SMARTY_COMPILED_TEMPLATES_CACHE,
					TIMTHUMB_CACHE,
					TIMTHUMB_TEMP_DIR,
					SQLITE_DIR,
					LOG_DIR,
					PH_CACHE,
					HTML_PURIFIER_CACHE);
?>
