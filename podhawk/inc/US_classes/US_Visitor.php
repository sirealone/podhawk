<?php

class US_Visitor
{
	private static $instance;
	private $previousVisitor = false;
	private $recordLife ; // how long (seconds) we want to keep records of visitors
	private $ip; // visitor's IP address

	private function __construct()
	{
		$this->ip = $_SERVER['REMOTE_ADDR'];

		$this->recordLife = VISITOR_RECORD_LIFE;

		$this->now = time();

		$this->clearTime = $this->now - $this->recordLife;
	}

	static function instance()
	{
		if (!isset (self::$instance))
		{
			self::$instance = new self();
		}
	return self::$instance;		
	}

	public function getVisitors($seconds)
	{
		if (ctype_digit($seconds))
		{
			$clearTime = $this->now - $seconds;
			$dosql = "SELECT COUNT(*) FROM ".DB_PREFIX."lb_visitors WHERE time > :time";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$result = $GLOBALS['lbdata']->executePreparedStatement(array(':time' => $clearTime));

			 //postgres and mysql return 'count' in different ways
			$visitors = (isset($result[0]['COUNT(*)'])) ? $result[0]['COUNT(*)'] : $result[0]['count'];

			return $visitors;
		}
		else
		{
			return false;
		}
	}

	public function countVisitor()
	{
		$this->deleteOldVisitors();

		$newVisitor = $this->checkPreviousVisit();

		if($newVisitor)
		{
			$this->addToDatabase();
		}

		else
		{
			$this->amendDatabase();
		}
	}

	private function deleteOldVisitors()
	{
		$dosql = "DELETE FROM ".DB_PREFIX."lb_visitors WHERE time < :time";
		$GLOBALS['lbdata']->prepareStatement($dosql);
		$GLOBALS['lbdata']->executePreparedStatement(array(':time' => $this->clearTime));
	}

	private function checkPreviousVisit()
	{
		$dosql = "SELECT COUNT(ip) FROM ".DB_PREFIX."lb_visitors WHERE ip = :ip";		
		$GLOBALS['lbdata'] -> prepareStatement($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':ip' => $this->ip));

		$onCount = (isset($result[0]['COUNT(ip)'])) ? $result[0]['COUNT(ip)'] : $result[0]['count'];

		return empty($onCount);
	}

	private function addToDatabase()
	{
		$dosql = "INSERT INTO ".DB_PREFIX."lb_visitors (time, ip) VALUES (:time, :ip)";
  		$GLOBALS['lbdata']->prepareStatement($dosql);
		$GLOBALS['lbdata']->executePreparedStatement(array(':time' 	=> $this->now,
															':ip' 	=> $this->ip));
	}

	private function amendDatabase()
	{
		$dosql = "UPDATE ".DB_PREFIX."lb_visitors SET time = :time WHERE ip = :ip";
   		$GLOBALS['lbdata']->prepareStatement($dosql);
		$GLOBALS['lbdata']->executePreparedStatement(array(':time' 	=> $this->now,
															':ip'	=> $this->ip));
	}
}
?>
