<?php

class PO_Comment
{
	private $posting_id;
	private $postingCommentStatus = '0'; // the 'comment_on' column in the posting data
	private $maxAudioSize = 0; // the 'comment_size' column in the posting data
	private $commentSetting = 'none'; // the comment option selected on the settings page
	private $usePHCommentSystem = false; // are we using either loudblog or akismet
	private $reg; // instance of Registry
	public  $newComment; // an instance of PO_New_NonAudio_Comment or PO_New_Audio_Comment
	private $dataPosted = false; // have we just posted a new comment to the database?

	public function __construct($id, $postingCommentStatus, $maxAudioSize=0)
	{
		$this->posting_id = $id;

		$this->maxAudioSize = $maxAudioSize;

		$this->postingCommentStatus = $postingCommentStatus;

		$this->reg = Registry::instance();

		$this->commentSetting = $this->reg->findSetting('acceptcomments');

		$this->usePHCommentSystem = ($this->commentSetting == 'loudblog' || $this->commentSetting == 'akismet');
	}

	public static function commentDataToProcess()
	{
		$reg = Registry::instance();

		// test if we have comment POST data to process AND that the request is for a single posting AND 
		// that we have enabled a Podhawk-based commenting system in settings.
		return	(	
				isset($_POST['commentname'])
				&& isset($_GET['id'])
				&& ($reg->findSetting('acceptcomments') == 'loudblog'
				|| $reg->findSetting('acceptcomments') == 'akismet'));
				
	}

	public function prepareComment()
	{
		$warning = $this->checkWarningStatus();

		$treat_as_preview = (isset($_POST['commentpreview']) || $warning);		
			
		// prepares POSTed comment data for assignment to Smarty
		$comment_data['name'] = '';
		$comment_data['mail'] = '';
		$comment_data['web'] = '';
		$comment_data['message'] = '';
		$comment_data['nospam'] = '';
		
		if (isset($_POST['commentname']) && $treat_as_preview)
		{
    		$comment_data['name'] = trim(entity_encode(stripslashes($_POST['commentname'])));
		}
		
		// for email address, we want to display the erroneous text which the user has entered ..
		if (isset($_POST['commentmail']))
		{
			if ($warning)
			{
				$comment_data['mail'] = trim($_POST['commentmail']);
			}
			elseif (isset($_POST['commentpreview']))		
			{
				$comment_data['mail'] = trim(stripslashes(allowed_characters($_POST['commentmail'], 'email')));
			}
		}
	
		// .. ditto for web address
		if (isset($_POST['commentweb']))
		{
			if ($warning)
			{
				$comment_data['web'] = trim($_POST['commentweb']);
			}
			elseif (isset($_POST['commentpreview']))
			
			{
				$comment_data['web'] = trim(stripslashes(allowed_characters($_POST['commentweb'],'web')));
			}
		}			

		if (isset($_POST['commentmessage']) && $treat_as_preview)
		{
      		$comment_data['message'] = trim(stripslashes($_POST['commentmessage']));
		}

		if (isset($_POST['commentspam']) && $treat_as_preview)
		{
    		$comment_data['nospam'] = trim(entity_encode(stripslashes($_POST['commentspam'])));
		}

		return $comment_data;
	}

	public function sendData()
	{
	// have we got the data needed to display a "submit" button?
		$haveData = (
					(isset($_POST['commentmessage']) AND trim($_POST['commentmessage']) != "") // a comment message  
   					OR
   					(isset($_FILES['commentfile']) AND $_FILES['commentfile']['error'] == "0") // a comment file
   					OR
   					(isset($_SESSION['filethrough']) AND ($_SESSION['filethrough'] != "")) // passed through from earlier preview
				);

		return ($haveData == true && $this->dataPosted == false );
	}

	public function acceptComments()
	// do we display a comments section
	{
		return ($this->commentSetting != 'none' && $this->postingCommentStatus > 0);
	}

	public function showCommentForm()
	// show the comment form if we are using akismet or loudblog commenting AND comments are active
	// (not closed) on the posting 
	{
		return 	($this->usePHCommentSystem && $this->postingCommentStatus == '1');					
	}

	public function processNewComment()
	{
		if ((isset($_FILES['commentfile'])) && $_FILES['commentfile']['error'] == "0" || isset($_SESSION['filethrough']))
		{
			$this->newComment = new PO_New_Audio_Comment($this->posting_id, $this->maxAudioSize);
		}
		else
		{
			$this->newComment = new PO_New_NonAudio_Comment($this->posting_id);
		}

		$this->newComment->process();

		$this->dataPosted = $this->newComment->clearCache();
		
	}

	private function checkWarningStatus()
	{
		// have we tried to process some new comment information?
		if (isset($this->newComment))
		{
			// was a warning thrown?
			$w = $this->newComment->getWarning();
			$warningStatus = (!empty($w));
		}
		else
		{
			$warningStatus = false;
		}

		return $warningStatus;
	}

}
?>
