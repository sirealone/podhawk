<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


// A PodHawk plugin

//Smarty modifier - converts date to
//full rfcc 2822 format Example: Thu, 21 Dec 2000 16:01:07 +0200
//
// Example of use in template :
//
// <pubDate>{$posting.posted|rfccdate}</pubDate>

function smarty_modifier_rfccdate ($date) {
   
    return date("r",strtotime($date));
}
?>
