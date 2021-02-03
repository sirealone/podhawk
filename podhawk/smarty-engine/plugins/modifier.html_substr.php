<?php
/*
* Smarty plugin
*
-------------------------------------------------------------
* File: modifier.html_substr.php
* Type: modifier
* Name: html_substr
* Version: 1.0
* Date: June 19th, 2003
* Purpose: Cut a string preserving any tag nesting and matching.
* Install: Drop into the plugin directory.
* Author: Original Javascript Code: Benjamin Lupu <lupufr@aol.com>
* Translation to PHP & Smarty: Edward Dale <scompt@scompt.com>
* Modification to add a string: Sebastian Kuhlmann <sebastiankuhlmann@web.de>
* Modification to put the added string before closing <p> or <li> tags
* and to add link to addstring, by Peter Carter http://www.podhawk.com
-------------------------------------------------------------
*/
function smarty_modifier_html_substr($string, $length, $addstring="", $link="")
{

    //some nice italics for the add-string
     if (!empty($addstring))
    {
	    $addstring = "<i> " . $addstring . "</i>";
	    if (!empty($link))
	    {
		    $addstring = "<a href=\"" . $link . "\">" . $addstring . "</a>";
	    }
    }

    $s = new HT_Shortener($string, $length);

    $s->setAddString($addstring, 'in');

    $return = $s->getShortText();
	
	return $return;
}
?>
