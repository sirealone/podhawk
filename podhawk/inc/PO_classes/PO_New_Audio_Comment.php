<?php

class PO_New_Audio_Comment extends PO_New_Comment
{
	private $permissions; // instance of Permissions
	private $maxAudioSize; // comment_size col of posting
	private $audioCommentsDir; // where audio comments are stored
	private $suffix; // the suffix (mp3 or ogg) of the comment file

	public function __construct($id, $maxAudioSize)
	{		
		parent::__construct($id);

		$this->maxAudioSize = $maxAudioSize;

		// path from server root to audio comments directory
		$this->audioCommentsDir = resolveDir(AUDIO_COMMENTS, PATH_TO_ROOT);

		try
		{
			$this->permissions = new Permissions(array(AUDIO_COMMENTS));

			if (!empty($_FILES['commentfile']['name']))
			{
				if ($_FILES['commentfile']['error'] != 0)
				{
					$err = $_FILES['commentfile']['error'];
					throw new Exception("There was an error in your file upload. The error code was $err which means " . DataTables::uploadErrors($err));
				}

				if ($_FILES['commentfile']['size'] > $this->maxAudioSize)
				{
					throw new Exception("Sorry! The file size of your audio comment ({$_FILES ['commentfile']['size']}) is too big. The max size is {$this->maxAudioSize}.");
				}
				
				// reject files with more than ane suffix (eg myfile.php.mp3)
				if (substr_count($_FILES['commentfile']['name'], '.') > 1)
				{
					throw new Exception("Uploaded file {$_FILES['commentfile']['name']} has been rejected as it appears to have more than one extension");
				}
				
				$this->suffix = strrchr($_FILES['commentfile']['name'], '.');
				if ($this->suffix != '.mp3' && $this->suffix != '.ogg')
				{
					throw new Exception ("The type of file you have uploaded ($suffix) is not allowed.");
				}
				
			}
			elseif (isset($_SESSION['filethrough']))
			{
				$this->suffix = strrchr($_SESSION['filethrough'], '.');
			}

			$this->audio_type = ($this->suffix == '.mp3') ? 1 : 3;

		}
		catch (Exception $e)
		{
			$this->log->write ('Unsuccessful attempt to upload a comment file. The error message was ' . $e->getMessage());
			die ($e->getMessage());
		}

	}

	public function process()
	{
		$this->permissions->make_writable(AUDIO_COMMENTS);

		try
		{
			if (isset($_POST['commentpreview']))
			{
				if (!empty($_FILES['commentfile']['name']))
				{
					// move uploaded file and give it a new name
					$newName = $this->audioCommentsDir . $this->newFileName('preview');
			
					$success = move_uploaded_file($_FILES['commentfile']['tmp_name'], $newName);
					
					if ($success == false)
					{
						throw new Exception('I have not been able to move the uploaded file.');
					}
	 
					chmod ($newName, 0600);

					$_SESSION['filethrough'] = $newName;
				}
			}
			elseif (isset($_POST['commentsubmit']))
			{			
				$newName = $this->audioCommentsDir . $this->newFileName('comment');
	
				if (!empty($_FILES['commentfile']['name']))
				{

					$success = move_uploaded_file($_FILES['commentfile']['tmp_name'], $newName);
					
					if ($success == false)
					{
						throw new Exception('I have not been able to move the uploaded comment file.');
					}
	 
				}
				elseif (!empty($_SESSION['filethrough']))
				{

					$success = rename($_SESSION['filethrough'], $newName);

					if ($success == false)
					{
						throw new Exception('I have not been able to rename the temporary comment file');
					}

					unset ($_SESSION['filethrough']);
				}

				chmod ($newName, 0600);

				$this->commentVariables();

				$this->audio_file = $newName;

				if ($this->audio_type == 1)
				{
					$id3 = new ID_ReadID3($newName);

					$this->audio_length = $id3->getSeconds();
					$this->audio_size = $id3->getSize();
				}

				$this->checkSpam();
		
				if ($this->is_spam == false)
				{
					$success = $this->commentToDatabase();
				}				
			}
		}
		catch (Exception $e)
		{
			$this->log->write("Error in handling an uploaded comment file. Error message was {$e->getMessage()},");

			$this->permissions->make_not_writable(AUDIO_COMMENTS);

			die($e->getMessage());
		}	 
		
		$this->deleteOrphans();

		$this->permissions->make_not_writable(AUDIO_COMMENTS);
	}

	private function newFileName($preview)
	{
		$daysec = 10000 + date("G")*3600 + date("i")*60 + date("s");
		
		$prefix = ($preview == 'preview') ? 'tempcom' : 'comment';

		$newName = $prefix . '-' . generatePassword(8) . '-' . date("Y-m-d-") . $daysec . $this->suffix;
	
		return $newName;
	}

	
	private function deleteOrphans()
	// removes any temporary comment files which are more than an hour old.
	{
		$handle = opendir($this->audioCommentsDir);

		while ($file = readdir($handle))
		{
			if (substr($file, 0, 8) == "tempcom-")
			{
				$filename  = substr($file, 0, strpos($file, "."));
            	$filestamp = strtotime(substr($filename, -16, 10)) + substr($filename, -5) - 10000;
            	$dropstamp = time() - 3600;

				if ($filestamp < $dropstamp)
				{
                	$success = @unlink($this->audioCommentsDir . $file);

					if (!$success)
					{
						$this->log->write("Failed to delete temporary comments file $file which is more than 1 hour old.");
					}
					else
					{
						$this->log->write("Deleted temp comments file $file");
					}
            	}
			}
		}
	}

}
?>
