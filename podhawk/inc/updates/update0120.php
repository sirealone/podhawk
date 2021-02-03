<?php
	if (DB_TYPE == 'mysql')
	{
		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ADD edited_with INT(2) DEFAULT '0'";
		$GLOBALS['lbdata']->Execute($dosql);

		$dosql = "ALTER TABLE ".DB_PREFIX."lb_postings ADD edit_date DATETIME DEFAULT NULL";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	if (DB_TYPE == "sqlite")
	{
		include INCLUDE_FILES . '/updates/update_functions.php';
	
		sqlite_add_columns('postings', 'edited_with','0');
		sqlite_add_columns('postings', 'edit_date', "''");
	}

	$dosql = "INSERT INTO ".DB_PREFIX."lb_settings (name, value) VALUES ('error_reporting','2')";
	$GLOBALS['lbdata']->Execute($dosql);
?>
