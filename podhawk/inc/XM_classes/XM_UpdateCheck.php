<?php

class XM_UpdateCheck extends XM_XML
{

	public function check()
	{
		if (!$this->file)
		{
			return "Sorry - I cannot read the xml file which contains information about updates";
		}

		$temp_array = array('release_notes', 'message', 'update_message', 'up_to_date_message');
		$reg 		= Registry::instance();
		$lang 		= $reg->findSetting('language');

		foreach ($temp_array as $item)
		{
			$$item = $item . "_" . $lang;

			if(empty($this->file->$$item))
			{
				$$item = $item . "_english";
			}
		}
				
		if ($this->file->latest_version > PH_VERSION)
		{
			$return = $this->file->$update_message." ".$this->file->$release_notes." ".$this->file->$message;
		}
		else
		{
			$return = $this->file->$up_to_date_message." ".$this->file->$message;
		}

		return entity_encode($return);
	}
}
?>
