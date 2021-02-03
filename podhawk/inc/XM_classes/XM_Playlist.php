<?php

class XM_Playlist extends XM_XML
{

	public function allMP3()
	{
		$allMP3 = true;
		
		try
		{
			libxml_use_internal_errors(true);
			
			if (empty($this->file->trackList))
			{
				throw new Exception("The XML file is not a PodHawk playlist file. There is no trackList");
			}

			if (empty($this->file->trackList[0]))
			{
				throw new Exception("The tracklist is empty");
			}

			foreach ($this->file->trackList->track as $track)
			{
				$errors = libxml_get_errors();

				if (!empty($errors))
				{
					$this->log->writeArray($errors);
					throw new Exception("There are errors in the XML file.");
				}
				
				if (getExtension($track->location) != 'mp3')
				{
					$allMP3 = false;
				}
			}
		}
		catch (Exception $e)
		{
			$allMP3 = false;
			$this->log->write ("Error in execution of XM_Playlist::allMP3(). " . $e->getMessage());
		}
		
		return $allMP3;
	}
}
?>
