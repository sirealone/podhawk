<?php

class DB_PDOWrapper extends DB_Wrapper

{
	private $statementHandle;
	private $selectQuery = false;

	public function __construct($db_type)
	{
		$this->log = LO_ErrorLog::instance();

		try
		{
			if ($db_type == 'mysql')
			{
				$login = $this->getConnectionParams();
				$this->db = new PDO("mysql:host=" . $login['host'] . ";dbname=" . $login['name'], $login['user'], $login['password']);
			}
			elseif ($db_type == 'postgres')
			{
				$login = $this->getConnectionParams();
				$this->db = new PDO("pgsql:host=" . $login['host'] . ";dbname=" . $login['name'], $login['user'], $login['password']);
			}
			elseif ($db_type == 'sqlite')
			{
				$this->db = new PDO("sqlite2:" . $this->sqliteLocation());
			}
			elseif ($db_type == 'sqlite3')
			{
				$this->db = new PDO("sqlite:" . $this->sqliteLocation());
			}

		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

			$count = $this->db->exec($dosql);
			
			return true;
		}
		catch (PDOException $e)
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

			$stmt = $this->db->query($dosql);
			
			$result = ($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;

			return $result;
		}
		catch (PDOException $e)
		{
			$this->DBExceptionHandler($e);
		}
	}

	public function GetAssoc($dosql)
	{
		$results = $this->GetArray($dosql);
	
		foreach ($results as $result)
		{
			if (count($result) == 2) // if there are two columns in the db table
			{				
				$name = reset($result); // set the name as the value in the first column
				$value = end($result); // and the value as the value in the second column
				$return[$name] = $value; // to give as simple name=>value array element				
			}
			else
			{
				// set the name as the value of the first column (typically the autoincrementing id column)
				// and the value as an associative array (colname=>colvalue) of the values in all the columns
				$return[reset($result)] = $result; 
			}
		}
		return $return;
	}

	public function qstr($string)
	{
		return $this->db->quote($string);
	}

	public function prepareStatement ($sql)
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
				{
					$this->log->write("Preparing statement $sql");
				}
	
			$this->statementHandle = $this->db->prepare($sql);

			if (substr(trim($sql), 0, 7) == "SELECT ")
			{
				$this->selectQuery = true;
			}
			else
			{
				$this->selectQuery = false;
			}
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}
	}

	public function bindParameter($name, $value)
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
				{
					$this->log->write("Binding $value to $name");
				}
			$this->statementHandle->bindParam($name, $value);
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}
	}

	public function executePreparedStatement($array)
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
				{
					$this->log->write("Executing prepared statement");
				}		
			$success = $this->statementHandle->execute($array);
		
			if ($this->selectQuery == true && $success == true) // if we have a SELECT query, we want to return an array of selected rows
			{
				return $this->statementHandle->fetchAll(PDO::FETCH_ASSOC);
			}
			else // else we want to know whether the query has executed successfully
			{
				return $success;
			}
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}

	// support for transactions (NB for MySQL requires InnoDB engine)
	public function beginTransaction()
	{
		try
		{
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
				{
					$this->log->write("Beginning transaction");
				}
			$this->db->beginTransaction();
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
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
				{
					$this->log->write("Committing transaction");
				}
			$this->db->commit();
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
			if (DEBUG == 'db_queries' || DEBUG == 'verbose')
				{
					$this->log->write("Rolling back transaction");
				}
			$this->db->rollBack();
		}
		catch (Exception $e)
		{
			$this->DBExceptionHandler($e);
		}	
	}		

}

?>
