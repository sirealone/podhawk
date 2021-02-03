<?php

class PO_Find_Audio_Comment
{
	private $log;
	private $requiredFile; // the address (relative to server root) of the required audio file
	private $publicName = ''; // the name of the file when it is downloaded

	public function __construct()
	{
		$actiontype = array('find_audio');
		include INCLUDE_FILES . '/authority.php';

		$this->log = LO_ErrorLog::instance();

		try
		{

			foreach ($_GET as $key => $value)
			{
				if ($key != 'com')
				{
					throw new Exception("Attempt to instantiate PO_Find_Audio_Comment with unsupported GET $key.");				
				}
				elseif (substr($value, 0, 7) == 'preview')
				{
					if (empty($_SESSION['filethrough']))
					{
						throw new Exception("I cannot find the audio file because session variable 'filethrough' is empty.");
					}

					$this->requiredFile = $_SESSION['filethrough'];
					$ext = getExtension($this->requiredFile);
					$this->publicName = "Comment-preview.$ext";

				}
				elseif (substr($value, 0, 1) == 'c')
				{
					$this->requiredFile = $this->findAudioComment($value);
					$this->publicName = "Comment-" . substr($value, 1);
				}
				else
				{
					throw new Exception("Attempt to instantiate PO_Find_Audio_Comments with unsupported value $value for GET['com'].");
				}
			}
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			include (INCLUDE_FILES . '/notfound.php');
		}
	}

	public function get()
	{
		if ($this->requiredFile)
		{
			$sender = new PO_File_Sender($this->requiredFile);

			if (!empty($this->publicName))
			{
				$sender->setFileName($this->publicName);
			}

			$sender->send();
		}
		else
		{
			include (INCLUDE_FILES . '/notfound.php');
		}
		
	}

	private function findAudioComment($value)
	{
		try
		{
			$bits = explode('.', $value);
			$id = substr($bits[0], 1);
			if (!ctype_digit($id))
			{
				throw new Exception("Attempt to access an audio comment with illegal value $id for comment id.");
			}

			$dosql = "SELECT audio_file FROM " . DB_PREFIX . "lb_comments WHERE id = :id";
			$GLOBALS['lbdata']->prepareStatement($dosql);
			$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $id));

			if (empty($result))
			{
				throw new Exception("Failed to find comment id $id in database");
			}

			if (empty($result[0]['audio_file']))
			{
				throw new Exception("Failed to find an audio file associated with comment id $id.");
			}
	
			return $result[0]['audio_file'];

		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			return false;
		}
	}
}
?>
