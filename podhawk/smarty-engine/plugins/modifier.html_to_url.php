<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


// A PodHawk plugin

//Smarty html_to_url plugin
//changes html-encoded entities back to 'normal' text,
//and then url encodes the text for inclusion in the query section of a url
//
// Example of use in template :
//
// <a href="index.php?tag={$tag|html_to_url}">Some content</a>

function smarty_modifier_html_to_url($text) {
	$s = my_html_entity_decode($text);
	$s = rawurlencode($s);
	return $s;
}
?>
