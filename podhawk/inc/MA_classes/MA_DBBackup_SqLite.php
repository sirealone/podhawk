<?php

class MA_DBBackup_SqLite extends MA_DBBackup

{
	protected function makeBackup()
	{
		$this->temp_file_location = AUDIOPATH . 'loudblogdata_backup_' . date("Y-m-d") . '.db';

		$success = copy (SQLITE_DIR . 'loudblogdata.db', $this->temp_file_location);

		if (!$success)
		{
			throw new Exception ('Failed to copy the SQLite database to the audio directory');
		}

		if (extension_loaded('zip'))
		{
			$zip = new ZipArchive();

			$zipDest = AUDIOPATH . 'loudblogdata_backup_' .date("Y-m-d") . '.zip';

			$res = $zip->open($zipDest, ZipArchive::CREATE);

			if (!$res)
			{
				throw new Exception("Failed to open $zipDest for writing");
			}

			$zip->addFile($this->temp_file_location);
		
			$success = $zip->close();

			if(!$success)
			{
				throw new Exception ("Failed to add file {$this->temp_file_location} to zip file.");
			}

			unlink ($this->temp_file_location);

			$this->temp_file_location = $zipDest;
			
		}

		return (file_exists($this->temp_file_location) && filesize($this->temp_file_location) > 100); 
	}
}
?>
