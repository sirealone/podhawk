<?php

class LO_ErrorLog
{
	private static $instance;

	static public function instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new LO_LogWriter('errors');
		}
		return self::$instance;
	}
}
?>
