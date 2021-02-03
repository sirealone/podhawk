<?php

class Autoupdate
{
	private $currentVersion;
	private $message = '';
	private $log;
	private $updated = false; // flag to indicate that we have updated the database on this run

	public function __construct($phVersion)
	{
		// turn ph version from '1.8' to 0180
		$this->currentVersion = $phVersion * 100;
		$this->log = LO_EventLog::instance();
	}

	public function update()
	{
		$updateArray = $this->getAvailableUpdates();

		$updateVersion = '';

		try
		{
			foreach ($updateArray as $updateVersion => $fileName)
			{			
				include INCLUDE_FILES . '/updates/' . $fileName;			
			}		

			if (!empty($updateVersion))
			{
				$updateVersion = (string)($updateVersion/100);
			
				$this->updateVersionNumber($updateVersion);
				$this->message = 'Updated to PodHawk ' . $updateVersion;
				$this->log->write('Updated database to PodHawk ' . $updateVersion);				
			}
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			$this->message = "There was an error in updating your database to PodHawk $updateVersion. " . $e->getMessage();
		}

		$clearCache = DA_CacheClear::instance(); // set flags to clear caches at end of programme run
		$toClear = array('SmartyCache', 'SmartyCompiledTemplates', 'PHCache', 'Registry');
		$clearCache->setFlag($toClear);
	}

	public function getMessage()
	{
		return $this->message;
	}

	private function getAvailableUpdates()
	{
		// get array of files which are in the updates directory...
		$updateArray = array();
		$updateFiles = get_dir_contents (INCLUDE_FILES . '/updates');

		foreach ($updateFiles as $file)
		{
			$updateVersion = substr($file, -8, -4);
			// .. and where the version indicator is greater than the current ph version
			if (ctype_digit($updateVersion) && $this->currentVersion < (int)$updateVersion)
			{
				$updateArray[$updateVersion] = $file;
			}
		}

		asort($updateArray); // vital to get them in the correct order!

		return $updateArray;
	}

	private function updateVersionNumber($updateVersion)
	{
		$dosql = 'UPDATE ' . DB_PREFIX . "lb_settings SET value = :value WHERE name = :name";
		$GLOBALS['lbdata'] -> prepareStatement ($dosql);
		$GLOBALS['lbdata'] -> executePreparedStatement(array(':value' => $updateVersion,
																':name' => 'ph_version'));
	}
	
}
?>
