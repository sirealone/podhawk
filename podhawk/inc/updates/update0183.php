<?php

	if (DB_TYPE == 'mysql')
	{
		$dosql = array ("ALTER TABLE ".DB_PREFIX."lb_categories ADD hide INT(2) DEFAULT '0'",
						"ALTER TABLE ".DB_PREFIX."lb_authors ADD hide INTEGER DEFAULT '0'",
						"ALTER TABLE ".DB_PREFIX."lb_postings ADD addfiles TEXT default ''",
						"ALTER TABLE ".DB_PREFIX."lb_postings DROP link");

		foreach ($dosql as $command)
		{
			$GLOBALS['lbdata']->Execute($command);
		}		
	}

	elseif (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7')
	{
		$dosql = array ("ALTER TABLE ".DB_PREFIX."lb_categories ADD COLUMN hide INTEGER DEFAULT '0'",
						"ALTER TABLE ".DB_PREFIX."lb_authors ADD COLUMN hide INTEGER DEFAULT '0'",
						"ALTER TABLE ".DB_PREFIX."lb_postings ADD COLUMN addfiles TEXT default ''",
						"ALTER TABLE ".DB_PREFIX."lb_postings DROP COLUMN link");

		foreach ($dosql as $command)
		{
			$GLOBALS['lbdata']->Execute($command);
		}
	}

	elseif (DB_TYPE == "sqlite" || DB_TYPE == "sqlite3")
	{
		include INCLUDE_FILES .'/updates/update_functions.php';

		sqlite_add_columns('categories', 'hide INT(2)', "'0'");
		sqlite_add_columns('authors', 'hide INTEGER', "'0'");
		sqlite_add_columns('postings', 'addfiles TEXT', "''");
		// dropping a column is unspeakably tedious in SQLite. So lets leave the 'link' column as a widow/orphan!
	}
?>
