<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

// a PodHawk plugin
//turns bytes into megabytes
//
// Example of use in template :
//
// <a href="{$posting.audiourl}">&nbsp;Get {$posting.mediatypename} ({$posting.audio_size|getmegabytes} MB | {$posting.audio_length|getminutes} min)</a>

function smarty_modifier_getmegabytes ($request) {
    $mb = $request / 1024 / 1024;
    $mb = round ($mb, 1);
    if ($mb == 0) { $mb = 0.1; }
    if ($request < 10) { $mb = 0; };
    return $mb;
}
?>
