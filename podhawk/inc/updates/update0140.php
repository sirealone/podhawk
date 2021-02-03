<?php

	$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('preferred_date_format', '%b %e, %Y')";
	$GLOBALS['lbdata']->Execute($dosql);

	$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('template_language', 'english')";
	$GLOBALS['lbdata']->Execute($dosql);

?>
