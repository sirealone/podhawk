<?php

if (!function_exists('sqlite_add_columns'))
{
	function sqlite_add_columns($table, $new_columns, $new_data)
	{
		//ADOdb supports sqlite2, not sqlite3, and sqlite2
		//requires this convoluted procedure to add a column to a table

		//if we try to add new columns which are already in the table, we risk losing the table completely
		if (sqlite_column_test($table, $new_columns))
		{
			return;
		}
		else
		{

			$table = DB_PREFIX."lb_".$table;

			//get the definition of $table
			$dosql = "SELECT * FROM sqlite_master WHERE tbl_name = '".$table."' AND type = 'table'";
			$result = $GLOBALS['lbdata']->GetArray($dosql);

			//create a temporary table and transfer everything in $table to it
			$dosql = "CREATE TABLE temp AS SELECT * FROM ".$table;
			$GLOBALS['lbdata']->Execute($dosql);

			//destroy $table
			$dosql = "DROP TABLE ".$table;
			$GLOBALS['lbdata']->Execute($dosql);

			//make a new $table with the new columns added at the end
			$create_table = substr($result[0]['sql'],0,-1).",".$new_columns.")";
			if($GLOBALS['lbdata']->Execute($create_table))//if the new table is successfully created...
			{
				//transfer data from temp, adding data for the new columns
				$dosql = "INSERT INTO $table SELECT *, $new_data FROM temp";
				$GLOBALS['lbdata']->Execute($dosql);

			}
			else
			{

				//..else restore the old $table from temp
				$dosql = "CREATE TABLE ".$table." AS SELECT * FROM temp";
				$GLOBALS['lbdata']->Execute($dosql);
			}
			//destroy temp - its work is finished
			$dosql = "DROP TABLE temp;";
			$GLOBALS['lbdata']->Execute($dosql);
		}
	}
}	

if (!function_exists('sqlite_column_test'))
{
	function sqlite_column_test ($table, $column)
	{		
		$table = DB_PREFIX."lb_".$table;
		$dosql = "SELECT * FROM sqlite_master WHERE type = 'table' and name = '" . $table . "'";
		$result = $GLOBALS['lbdata']->GetArray($dosql);
		$sqlstring = $result[0]['sql'];
		$result = strpos($sqlstring, $column);
		return $result;
	}
}
?>
