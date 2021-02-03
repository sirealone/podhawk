<?php

// a PodHawk plugin
//reads the PodHawk 'visitors' table and returns the
//number of visitors in the last '$seconds' seconds.
//Works only if the PodHawk 'record visitors' setting is enabled
//
// Example use in template :
// {dynamic}
// <p>Visitors in last hour {count_visitors seconds=3600}</p>
// {/dynamic}
//
//The {dynamic} tag is needed to ensure that the plugin runs every time 
//even if the requested page is cached

function smarty_function_count_visitors($params)  {

$seconds = $params['seconds'];

$visitor = US_Visitor::instance();

return $visitor->getVisitors($seconds);

}
?>
