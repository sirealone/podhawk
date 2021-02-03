<?php

class RE_UploadBrowser extends RE_CopyFTP
{

	protected function getAudioFileName()
	{
		if ((!isset($_FILES['fileupload'])) || ($_FILES['fileupload']['error'] != "0"))
		{
			$this->message = 'uploadbroken';
			$this->id = ($this->update_id) ? $this->update_id : '';
			if (isset($_FILES['fileupload']['error']))
			{
				$err = $_FILES['fileupload']['error'];
				$this->log->write("File upload failed. The returned error code was $err, which means " . DataTables::uploadErrors($err));
			}
			else
			{
				$this->log->write("File upload failed. No error code was returned.");
			}
		}
		else
		{
			$this->file_name = $_FILES['fileupload']['name'];
		}
	}

	protected function copyFile()
	{
		$success = move_uploaded_file($_FILES['fileupload']['tmp_name'], $this->newFilePath);
		return $success;
	}
}
?>
