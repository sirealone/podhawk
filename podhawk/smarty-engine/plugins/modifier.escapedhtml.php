<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */




//escapes ampersands and chevrons

function smarty_modifier_escapedhtml($text) {
$trans_tbl = array (
    "&"=>"&amp;",
    "<"=>"&lt;",
    ">"=>"&gt;",
    "&rsquo;"=>"&apos;"
);
return trim(strtr($text, $trans_tbl));
}
?>
