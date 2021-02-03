<?php

// special PodHawk bit - find a configuration file if possible, and use 'PATH_TO_ROOT' to tell timthumb where to look for images files.
// The 'src' parameter sent to timthumb should be the path from the root of the PodHawk site eg 'images/image.jpg'

# Location of the config file. PodHawk will automatically look for a configuration file in podhawk/custom/.
## If it cannot find a configuration file there, then PodHawk will look in the directory
## above the root of your PodHawk site. This location needs to be inaccessible from the web.
## If you need to place your config file somewhere other than one of these two locations, enter the full path
## from your server root in the line below eg $path_to_config = "/home/www/mysite.com/config.php";
$path_to_config = "";

if (!defined('LOCAL_FILE_BASE_DIRECTORY')) // we can't define LOCAL_FILE_BASE_DIRECTORY twice
{
	if (!defined('PATH_TO_ROOT')) // if we haven't already run the configuration script
	{
		require_once '../inc/classes/Configuration.php';
		$config = new Configuration();
		if (!empty($path_to_config))
		{ 
			$config->setPathToConfig($path_to_config);
		}
		$configured = $config->configure();
	}
	if (defined('PATH_TO_ROOT')) // the config file should have defined PATH_TO_ROOT
	{
		define('LOCAL_FILE_BASE_DIRECTORY', PATH_TO_ROOT);
	}
}

// lets prevent hot linking to our thumbnail images
define ('BLOCK_EXTERNAL_LEECHERS', true);
?>
