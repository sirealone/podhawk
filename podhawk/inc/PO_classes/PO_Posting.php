<?php

class PO_Posting
{
	protected $posting = array(); //array of posting data
	protected $id; // id of posting
	protected $log; // instance of LogWriter
	protected $reg; // instance of Registry
	

	public function __construct($posting)
	{
		try
		{
			if (is_array($posting)) // an array of posting data has been received
			{
				if (isset($posting['id'])) // we try to find the posting id
				{
					$this->id = $posting['id'];
				}
				else
				{
					throw new Exception ('Cannot construct instance of ' . get_class($this) . '. Cannot find posting id.');
				}
					
				$this->posting = $posting;				
			}
			elseif (is_numeric($posting)) // alternatively we have received the posting id
			{
				$this->posting = $this->findPosting($posting);
				$this->id = $posting;
			}
			else
			{
				throw new Exception ('Cannot construct instance of ' . get_class($this) . '. Unsupported argument sent to constructor.');
			}
			
			$this->log = LO_ErrorLog::instance();
			$this->reg = Registry::instance();
		}

		catch (Exception $e)
		{
			$class = get_class($this);
			$message = $e->getMessage();
			$this->log->write($message); 
			die ("Error in constructing object of class $class. Error message was '$message'");			
		}	
	}

	public function getPosting()
	{
		return $this->posting;
	}

	public function getID()
	{
		return $this->id;
	}

	public function getCol($col)
	{
		return $this->posting[$col];
	}

	public static function findPosting($id)
	{
		$dosql = "SELECT * FROM " . DB_PREFIX . "lb_postings WHERE id = :id";

		$GLOBALS['lbdata']->prepareStatement($dosql);

		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $id));

		return (isset($result[0])) ? $result[0] : false;			
	}
}
?>
