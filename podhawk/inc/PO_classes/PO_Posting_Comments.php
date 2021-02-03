<?php

class PO_Posting_Comments
{

	private $posting_id;
	private $comments = array();
	private $count = 0; // number of comments

	public function __construct($id)
	{
		$this->posting_id = $id;

		$dosql = "SELECT * FROM " . DB_PREFIX . "lb_comments WHERE posting_id = :id ORDER BY posted DESC";
		$GLOBALS['lbdata'] -> prepareStatement($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $this->posting_id));

		$this->comments = (!empty($result)) ? $result : array();

		$this->count = count($this->comments);

		$this->expandComments();
	}

	public function getComments()
	{
		return $this->comments;
	}

	public function getCount()
	{
		return $this->count;
	}

	private function expandComments()
	{
		foreach ($this->comments as $key => $comment)
		{
			if ($comment['audio_type'] != '0')
			{ 
				$this->comments[$key]['mimetype'] = PO_Posting_Extended::getMimeType($comment['audio_type']);
				$this->comments[$key]['itunesduration'] = PO_Posting_Extended::getiTunesDuration($comment['audio_length']);
				$suffix = ($comment['audio_type'] == 1) ? '.mp3' : '.ogg';
				$this->comments[$key]['downloadlink'] = "get.php?com=c" . $comment['id'] . $suffix;
			}
		}

		if (isset($_POST['commentpreview']))
		{
			$this->addDummyComment();
		}
	}

	private function addDummyComment()
	{
		if (!empty($_SESSION['filethrough']))
		{
			$tempFileName = $_SESSION['filethrough'];
		
			if (strrchr($tempFileName, '.') == '.mp3')
			{
				$id3 = new ID_ReadID3($_SESSION['filethrough']);
		        $tempfilesize = $id3->getSize();
		        $tempfilelength = $id3->getSeconds();
			}
        }
		else
		{
			$tempFileName = '';
            $tempfilesize = "0";
            $tempfilelength = "0";
        }

		$name = (empty($_POST['commentname'])) ? 'Anonymous' : entity_encode(trim($_POST['commentname']));
		$ext = getExtension($tempFileName);

		$p = new HT_MakeCommentHTML;
		$message_html = $p->make($_POST['commentmessage']);

		$array = array(	'id' 			=> 0,
						'posting_id' 	=> $this->posting_id,
						'posted' 		=> date('Y-m-d H:i:s'),
						'name' 			=> $name,
						'mail' 			=> allowed_characters($_POST['commentmail'],'email'),
						'web' 			=> allowed_characters($_POST['commentweb'], 'web'),
						'ip' 			=> $_SERVER['REMOTE_ADDR'],
						'message_input' => $_POST['commentmessage'],
						'message_html' 	=> "<p>[PREVIEW]</p> " . $message_html,
						'audio_file' 	=> $tempFileName,
						'audio_size' 	=> $tempfilesize,
						'audio_length' 	=> $tempfilelength,
						'audio_type' 	=> DataTables::getAudioType($ext),
						'downloadlink'	=> 'get.php?com=preview.' . $ext
						);

		$this->comments[$this->count] = $array;
	}
}

?>
