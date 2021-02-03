<?php

$actiontype = array('backend');
include 'authority.php';

//ajax script for returning autocomplete lists of images for the id3 page or of tags for recording 2, playlist and categories pages
if (!isset($_GET['type']))
{
	echo "Error";
}
else
{

	if ($_GET['type'] == 'images')
	{
		$data = get_dir_contents("../images");
	}
	elseif ($_GET['type'] == "tags")
	{
		$tags = TagManager::instance();
		$data = $tags->getAllTagsList();
	}

	$q = isset($_GET['q']) ? $_GET['q'] : '';

	foreach ($data as $item)
	{
		if(@preg_match("/^" . $q . "/i", $item))
		{
			echo $item."\r\n";
		}
	}
}
?>
