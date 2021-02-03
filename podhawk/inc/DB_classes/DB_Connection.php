<?php

class DB_Connection
{

	private $use_ADODB;
	private $db_type;

	public function __construct($type)
	{
		if (strpos(strtolower($type), 'postgres') !== false)
		{
			$type = 'postgres';
		}

		$this->db_type = $type;

		$pdo_available = (extension_loaded('PDO') && $this->checkPDODrivers() == true);

		$this->use_ADODB = (FORCE_ADODB == true || $pdo_available == false); 
	}

	public function makeConnection()
	{
		if ($this->use_ADODB == true)
		{
			$connection = new DB_ADODBWrapper($this->db_type);
		}
		else
		{
			$connection = new DB_PDOWrapper($this->db_type);
		}
		
		$this->setCharSet($connection);

		return $connection;
	}

	public function getConnectionType()
	{
		return ($this->use_ADODB) ? 'ADODB' : 'PDO';
	}

	
	private function checkPDODrivers()
	{
		
		if ($this->db_type == 'mysql')
		{
			return (extension_loaded('pdo_mysql'));
		}
		elseif ($this->db_type == 'postgres')
		{
			return (extension_loaded('pdo_pgsql'));					
		}
		elseif ($this->db_type == 'sqlite' || $this->db_type == 'sqlite3')
		{
			return (extension_loaded('pdo_sqlite'));				
		}
		return false;
	}

	private function setCharSet($connection)
	{
		if ($this->db_type == 'mysql')
		{
			$dosql = "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'";
			$connection->Execute($dosql);
		}
		elseif ($this->db_type == 'postgres')
		{
			$dosql = "SET CLIENT_ENCODING TO 'UTF8'";
			$connection->Execute($dosql);
		}
	}

}
?>
