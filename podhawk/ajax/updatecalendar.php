<?php

define ('ACTION', 'webpage');

require "../initialise.php";

// what theme do we want?
$theme = (isset($_GET['theme']) && in_array($_GET['theme'], get_dir_contents(PATH_TO_ROOT."/podhawk/custom/themes"))) ? $_GET['theme'] : $reg->findSetting('template');

// instantiate a Smarty object
$smarty = new SM_FrontendAjax($theme, 'update_cal');

$smarty->clear_compiled_tpl();

// find the template language file
$t = new TR_TranslationWebpage($theme);
$smarty->assign('trans', array('days_string' => $t->getTrans('days_string')));

$smarty->assign('theme', $theme);

// get the html from the calendar template - no need to call the calendar function as the template already does so
$html = $smarty->fetch('sidebar_calendar.tpl');

// strip off the <div>..</div> tags from the html
$html = str_replace('<div id="calendar">', "", $html);

$html = str_replace("</div>", "", $html);

// and return the html in a JSON object
echo json_encode(array('html' => $html)); 
?>
