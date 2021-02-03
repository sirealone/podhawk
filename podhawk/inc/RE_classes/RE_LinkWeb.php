<?php

class RE_LinkWeb extends RE_NewAudioFile
{
	private $remote_file_atts;
	private $linkURL = '';

	public function __construct($update_id)
	{
		parent::__construct($update_id);
	
		if (!isset($_POST['linkurl']))
		{
			$this->message = 'noaudio';
			$this->newPostId = ($update_id) ? $update_id : '';
		}
		else
		{
			$this->linkURL = $this->addHttp($_POST['linkurl']);
			$this->file_name = $this->extractFileName($this->linkURL);
		}

	}


	public function makePosting()
	{
		if (!empty($this->file_name))
		{			
			$filetype = $this->getAudioType($this->linkURL);
			$filename = $this->stripSuffix($this->file_name);
			$fileatts = $this->getRemoteFileAttributes($this->linkURL);

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
				':audio_file' 		=> urldecode($this->linkURL),
				':audio_type' 		=> $filetype,
				':audio_size' 		=> $fileatts['size'],
				':audio_length' 	=> $fileatts['length']);
		
				$success = $this->insertDatabaseRow($insertData);

				$this->newPostId = $this->getNewPostId(urldecode($this->linkURL), false);				
			}
			else // update existing row and remove old audio file if there is one
			{
				$updateData = array(
				':author_id' 		=> $_SESSION['authorid'],
				':filelocal' 		=> 0,
				':audio_file' 		=> urldecode($this->linkURL),
				':audio_type' 		=> $filetype,
				':audio_size' 		=> $fileatts['size'],
				':audio_length' 	=> $fileatts['length'],
				':id'				=> $this->update_id);
				
				$success = $this->updateDatabaseRow($updateData);
							
				$this->newPostId = $this->update_id;
			}

			$this->message = ($success) ? "linksuccess" : "dbproblem";

			if ($this->message == "linksuccess" && $fileatts['size'] == 0)
			{
				$this->message = "linksuccess_but";
			}				
		}
	}

	private function getRemoteFileAttributes($url)
	{

	// Attempts to determine the size of the file given in
    // the supplied URL using HTTP/1.1.
    // Returns: the file size in bytes, null otherwise.
    // This script was written by Ektoras. Thank you very much!!!

		$return['size']   = 0;
    	$return['length'] = 0;

    

    	$parsedURL = parse_url($url);
    	$host      = $parsedURL['host'];
    	$port      = isset($parsedURL['port']) ? $parsedURL['port'] : 80;
    	$resource  = $parsedURL['path'];

    	// Connect to the remote web server.
    	$fp = @fsockopen ($host, $port);
    	if ($fp != false)
		{
		    // We are connected. Let's talk.
		    $headString       = sprintf("HEAD %s HTTP/1.1\r\n", $resource);
		    $hostString       = sprintf("HOST: %s\r\n", $host);
		    $connectionString = sprintf("Connection: close\r\n\r\n");

		    fputs($fp, $headString);
		    fputs($fp, $hostString);
		    fputs($fp, $connectionString);

		    $response = '';
		    while (!feof($fp))
			{
		        $response .= fgets($fp);
		    }

		    fclose ($fp);

		    // Examine the HTTP response header to determine the size of the resource.
		    if (preg_match('/Content-Length:\s*(\d+)/i', $response, $matches))
			{
		        $return['size'] = $matches[1];
		    }
    	}

	// TODO devise alternative method - temporary download of file - measure attributes.

    return $return;
	}


}
?>
