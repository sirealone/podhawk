<?php

class RE_JwExternalLink extends RE_NewAudioFile
{
	private $jwlinkurl = ''; // the url of the file to link to
	private $link_type = 0; // the type of link (eg flv, youtube)
	private $streamer = ''; // address of streamer
	private $streaming_file = ''; // name of streaming file
	private $is_streamer = false; // is the link to a streaming file?

	public function __construct($update_id, $url_here)
	{
		parent::__construct($update_id);

		$this->getJWData($url_here);		

		if (empty($this->jwlinkurl) && (empty($this->streamer) || empty($this->streaming_file)) || empty($this->link_type))
		{
			$this->message = 'noaudio';
			$this->newPostId = ($update_id) ? $update_id : '';
		}
		elseif ($this->is_streamer == true && (empty($this->streamer) || empty($this->streaming_file)))
		{
			$this->message = 'incomplete';
			$this->newPostId = ($update_id) ? $update_id : '';
		}
		
		if ($this->is_streamer == true)
		{
			$this->file_name = $this->streaming_file;
		}
		else
		{
			$this->file_name = $this->extractFileName($this->jwlinkurl);
		}	

	}

	public function makePosting()
	{
		$filename = $this->stripSuffix($this->file_name);
		$filetype = $this->getAudioType();

		$audio_file = ($this->is_streamer == false) ? $this->jwlinkurl : $this->streamer."/".$this->streaming_file;
		
		$success = false;

		if (empty($this->update_id)) // new post, new row in postings table
			{
				$insertData = array(
				':author_id' 		=> $_SESSION['authorid'],				
				':title' 			=> $filename,
				':posted' 			=> date("Y-m-d H:i:s"),
				':message_input' 	=> "",
				':message_html' 	=> "",				
				':filelocal' 		=> 0,
				':audio_file' 		=> urldecode($audio_file),
				':audio_type' 		=> $filetype,
				':audio_size' 		=> 0,
				':audio_length' 	=> 0,
				':jw_streamer'		=> urldecode($this->streamer),
				':jw_streaming_file'=> urldecode($this->streaming_file));
		
				$success = $this->insertDatabaseRow($insertData);

				$this->newPostId = $this->getNewPostId(urldecode($audio_file), false);
			}
			else // update existing row and remove old audio file if there is one
			{
				$updateData = array(
				':author_id' 		=> $_SESSION['authorid'],
				':filelocal' 		=> 0,
				':audio_file' 		=> urldecode($audio_file),
				':audio_type' 		=> $filetype,
				':audio_size' 		=> 0,
				':audio_length' 	=> 0,
				':jw_streamer'		=> urldecode($this->streamer),
				':jw_streaming_file'=> urldecode($this->streaming_file),
				':id'				=> $this->update_id);
				
				$success = $this->updateDatabaseRow($updateData);
							
				$this->newPostId = $this->update_id;
			}
	
			$this->message = ($success) ? "linksuccess" : "dbproblem";

	}

	protected function getAudioType()
	{
		switch ($this->link_type)
		{
			case 1 : $audio_type = 20; break;  // .flv
			case 2 : $audio_type = 21; break;  // YouTube video
			case 3 : $audio_type = 22; break;  // playlist or YouTube Playlist
			case 4 : $audio_type = 30; break;  // RTMP stream with single file
			case 5 : $audio_type = 31; break;  // RTMP stream with playlist
			case 6 : $audio_type = 32; break;  // HTTP stream with single file
			case 7 : $audio_type = 33; break;  // HTTP stream with playlist
			default : $audio_type = 0; break;	
		}
		
		return $audio_type;
	}

	private function getJWData($url_here)
	{
		if (!empty($_POST['jwlinkurl']) && trim($_POST['jwlinkurl']) != $url_here)
		{
			$url = trim($_POST['jwlinkurl']);
			$this->jwlinkurl = $this->addHttp($url);
		}
		
		if (!empty($_POST['jw_link_type']))
		{
			$this->link_type = $_POST['jw_link_type'];
		}

		if (!empty($_POST['streamer']))
		{
			$this->streamer = $_POST['streamer'];
		}

		if (!empty($_POST['file_to_stream']))
		{
			$this->streaming_file = $_POST['file_to_stream'];
		}	
		
		$stream_array = array(4,5,6,7);
		
		if (in_array($this->link_type, $stream_array))
		{
			$this->is_streamer = true;
		}
	}

}
?>
