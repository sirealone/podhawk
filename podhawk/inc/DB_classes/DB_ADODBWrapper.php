<?php

class DB_ADODBWrapper extends DB_Wrapper
{
	private $dummyStatement;
	private $functionPrefix;

	public function __construct($db_type)
	{
		$this->log = LO_ErrorLog::instance();

		if ($db_type == 'sqlite3')
		{
			$this->log->write("Attempt to use ADODB to open SQLite3 database at {$this->errLoc}");

			die ('ADODB Lite does not support SQLITE 3. Please use PDO to access your database.');
		}

		require_once PATH_TO_ROOT . "/podhawk/adodb_lite/adodb.inc.php";
		require_once PATH_TO_ROOT . '/podhawk/adodb_lite/adodb-exceptions.inc.php';

		// we want ADODB_lite to return associated arrays of DB Data
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		$this->getFunctionPrefix($db_type);	
		
		try
		{
			$this->db = &NewADOConnection($this->functionPrefix);

			if ($db_type == 'sqlite')
			{
				$e = $this->db->Connect($this->sqliteLocation());
			}
			else
			{
				$login = $this->getConnectionParams();
				$e = $this->db->Connect($login['host'], $login['user'], $login['password'], $login['name']);
			}
			if ($e == false)
			{
				throw new Exception ('Unable to connect to database ' . $login['name']);
			}
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}
	}		

	public function Execute($dosql)
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
			{
				$this->log->write($dosql);
			}

			$result = $this->db->Execute($dosql);
			return $result;
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}

	public function GetArray($dosql)
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
			{
				$this->log->write($dosql);
			}

			$result = $this->db->GetArray($dosql);
			return $result;
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}

	public function GetAssoc($dosql)
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
			{
				$this->log->write($dosql);
			}

			$result = $this->db->GetAssoc($dosql);
			return $result;
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}
	
	public function qstr($string)
	{
		return $this->db->qstr($string);
	}

	public function prepareStatement($sql)
	{
		$this->dummyStatement = $sql;
	}

	// the prepared statement uses :value placeholders
	public function executePreparedStatement($array)
	{
		try
		{
			$length = count($array); // how many :field items of data

			if ($length > 0)
			{
				$names = array_keys($array); // the names of the placeholders
				$temp = array_values($array); // the values we want to replace them with
				foreach ($temp as $item)
				{
					$values[] = $this->qstr($item); // escape the values
				}
				$dosql = str_replace($names, $values, $this->dummyStatement, $count);

				if ($count != $length)
				{
					throw new Exception ('Data array could not be matched with SQL statement ' . $this->dummyStatement);
				}
			}
			else
			{
				$dosql = $this->dummyStatement;
			}

			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
			{
				$this->log->write($dosql);
			}

			if (substr(trim(strtoupper($dosql)), 0, 6) == "SELECT") // return the result if we have a SELECT query
			{
				$return = $this->GetArray($dosql);
			}
			else // execute and return true or false
			{
				$return = $this->Execute($dosql);
			}
			return $return;
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}

	// transactions support
	public function beginTransaction()
	{
		try
		{
			if (class_exists($this->functionPrefix . '_transaction_ADOConnection')) // test if transaction module loaded
			{
				$this->db->BeginTrans();
			}
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}

	public function commit()
	{
		try
		{
			if (class_exists($this->functionPrefix . '_transaction_ADOConnection')) // test if transaction module loaded
			{
				$this->db->CommitTrans();
			}
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}

	public function rollBack()
	{
		try
		{
			if (class_exists($this->functionPrefix . '_transaction_ADOConnection')) // test if transaction module loaded
			{
				$this->db->RollbackTrans();
			}
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}		

	private function getFunctionPrefix($db_type)
	// translates PodHawk version of db type (eg 'postgres') into type name used by ADODB (eg 'pgsql')
	{
		if ($db_type == 'postgres')
		{
			$this->functionPrefix = 'pgsql';
		}
		elseif ($db_type == 'mysql')
		{
			$this->functionPrefix = 'mysqli';
		}
		else
		{
			$this->functionPrefix = $db_type;
		}
	}
}
?>
