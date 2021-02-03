<?php
// A PodHawk plugin
//
// turns html entities into UTF-8 or XML entities
// needed for creating RSS feed
//
// Example of use in template :
//
// <title>{$settings.sitename|htmltoxml}</title>

function smarty_modifier_htmltoxml ($text)
{
	return DataTables::html_to_xml($text);
}

?>
