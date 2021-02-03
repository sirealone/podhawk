<?php

class LO_EventLog
{
	private static $instance;

	static public function instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new LO_LogWriter('events');
		}
		return self::$instance;
	}
}
?>
