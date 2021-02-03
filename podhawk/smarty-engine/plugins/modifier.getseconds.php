<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */




//Smarty getmegabytes plugin
//turns a 3:33 string into seconds

function smarty_modifier_getseconds ($request) {
    $pieces = explode (":", $request);
    $sec = $pieces[0] * 60;
    $sec += $pieces[1];
    return $sec;
}
?>
