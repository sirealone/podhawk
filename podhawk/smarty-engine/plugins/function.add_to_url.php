<?php

// a PodHawk plugin
//
// takes the array $_GET for the current page, and adds to it attribute
// $param['att'] with value $param['value'], or amends the current value
// if $_GET[$param['att']] is already set, and returns a new attribute string
// eg "?page=1&amp;cat=  ...etc"
//
// Example of use in template :
//
// <a href="index.php{add_to_url att='atp' value='1'}">Some content</a>

function smarty_function_add_to_url ($params) {
    $return = "";

    foreach ($_GET as $oldatt => $oldvalue) {
    if ($oldatt != $params['att']) {
    $return .= "&amp;".$oldatt."=".urlencode($oldvalue);
    }
    }
    $return .= "&amp;".$params['att']."=".urlencode($params['value']);
    return "?".substr($return, 5);
}
?>
