<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


// A podHawk plugin
//turns seconds into minutes
//
// Example of use in template :
//
// <a href="{$posting.audiourl}">&nbsp;Get {$posting.mediatypename} ({$posting.audio_size|getmegabytes} MB | {$posting.audio_length|getminutes} min)</a>

function smarty_modifier_getminutes ($sec) {
    $min = (int) ($sec / 60);
    $min2 = $sec%60;
    if ($min2 < 10) { $min2 = "0" . $min2; }
    return $min.":".$min2;
}
?>
