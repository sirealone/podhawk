<?php

class RE_SavePosting
{
	protected $message;
	protected $edit_id = false;
	protected $reg; // instance of Registry
	protected $log; // instance of LogWriter
	protected $user; // instance of US_User
	protected $previousStatus;
	protected $errLoc; // a location identifier for error messages

	public function __construct($edit_id, $user)
	{
		$this->reg = Registry::instance();

		$this->log = LO_ErrorLog::instance();
		
		if (!isset($_GET['id']))
		{
			$this->message = 'noaudio';
		}
		else
		{
			$this->edit_id = $edit_id;
		}

		$this->user = $user;
	}	

	public function getMessage()
	{
		return $this->message;
	}

	public function getId()
	{
		return $this->edit_id;
	}

	public function getPreviousStatus()
	{
		return $this->previousStatus;
	}

	public function makePosting()
	{
		if ($this->edit_id)
		{
			$this->author_id = $this->getAuthorId();
			$success = false;
			$this->previousStatus = $this->findPreviousStatus();

			try
			{
				$this->errLoc = "method " . __METHOD__;				
				$GLOBALS['lbdata']->beginTransaction();

				// the form on recording page 2 disables certain fields according to the user's edit/publish privileges. 
				// Disabled fields do not send POST values.
				// So we need different procedures for writing to the database, also according to the user's privileges.

				if ($this->user->mayPublish($this->author_id)) // change the status if user may publish
				{
					$dosql = "UPDATE ". DB_PREFIX . "lb_postings SET status = :status WHERE id = :id";
					$inputArray = array (':status' => $_POST['status'],
											':id'	=> $this->edit_id);
					$GLOBALS['lbdata']->prepareStatement($dosql);				
					$success = $GLOBALS['lbdata']->executePreparedStatement($inputArray);

					if (!$success)
					{
						throw new Exception ("Unable to write new data for posting id {$this->edit_id} to database");
					}	
				}

				if ($this->user->mayEdit($this->author_id)) // change most other things if the user may edit
				{
					$dosql = $this->getDosql();
					$inputArray = $this->getInputArray();
					$GLOBALS['lbdata']->prepareStatement($dosql);				
					$success = $GLOBALS['lbdata']->executePreparedStatement($inputArray);

					if (!$success)
					{
						throw new Exception ("Unable to write new data for posting id {$this->edit_id} to database");
					}	

					$this->updateLinks();
				}
				
				if ($this->user->mayEditAll()) // change the author if user may edit all
				{
					$dosql = "UPDATE " . DB_PREFIX . "lb_postings SET author_id = :author_id WHERE id = :id";
					$inputArray = array (':author_id' => $_POST['author'],
											':id'		=> $this->edit_id);
					$GLOBALS['lbdata']->prepareStatement($dosql);				
					$success = $GLOBALS['lbdata']->executePreparedStatement($inputArray);

					if (!$success)
					{
						throw new Exception ("Unable to write new data for posting id {$this->edit_id} to database");
					}	

				}				

				$this->updatePreviews();				
				
				$GLOBALS['lbdata']->commit();

				$this->message = 'saved';
			}
			catch (Exception $e)
			{
				$GLOBALS['lbdata']->rollBack();

				$this->log->error($e, $this->errLoc);

				$this->message = 'dbproblem';
			}

			return $success;

		}
	}

	protected function getDosql()
	{
		$dosql = "UPDATE ".DB_PREFIX."lb_postings SET
		
						  	title           = :title,
						  	message_input   = :message_input,
						  	message_html    = :message_html,
							summary			= :summary,
						  	posted          = :posted,
						  	comment_on      = :comments_on,
						  	audio_length    = :audio_length, 
						  	audio_size      = :audio_size, 
						  	comment_size    = :comment_size,
							category1_id    = :category1_id,
							category2_id    = :category2_id,
							category3_id    = :category3_id,
							category4_id    = :category4_id,
						  	tags  	        = :tags,
							sticky	        = :sticky,
						  	itunes_explicit = :itunes_explicit,
							edited_with     = :edited_with,
							edit_date       = :edit_date,
							image           = :image,
							status			= :status 

						  	WHERE id = :id";
		return $dosql;
	}

	protected function getInputArray()
	{
		$image = (!empty($_POST['image'])) ? $_POST['image'] : '';

		// instantiate an object to make and clean html from $_POST['message']
		$makeHTML = new HT_MakeHTML($_POST['editor_used']);
		$message_html = $makeHTML->make($_POST['message']);

		// pass $_POST['status'] through if user can publish, or if the status change does not put the post on air, otherwise keep the previous status
		if ($this->user->mayPublish($this->author_id) || $_POST['status'] == '1' || $_POST['status'] == '2') $status = $_POST['status'];
		else $status = $this->previousStatus;		

		$inputArray = array(
					':title' 			=> entity_encode($_POST['title']),
					':message_input' 	=> entity_encode($_POST['message']),
					':message_html' 	=> $message_html,
					':summary' 			=> entity_encode($_POST['summary']),
					':posted' 			=> $this->getDate(),
					':comments_on' 		=> $_POST['comment_on'],
					':audio_length' 	=> $this->getDuration(),
					':audio_size' 		=> $this->getSize(),
					':comment_size' 	=> $_POST['comment_size'],
					':category1_id' 	=> $_POST['cat1'],
					':category2_id' 	=> $_POST['cat2'],
					':category3_id' 	=> $_POST['cat3'],
					':category4_id' 	=> $_POST['cat4'],
					':tags' 			=> entity_encode(str_replace(',', ' ', $_POST['tags'])),
					':sticky' 			=> $this->tickbox('sticky'),
					':itunes_explicit' 	=> $this->tickbox('itunes_explicit'),
					':edited_with' 		=> $_POST['editor_used'],
					':edit_date' 		=> date("Y-m-d H:i:s"),
					':image' 			=> $image,
					':id' 				=> $this->edit_id,
					':status'			=> $status
					);

		return $inputArray;

	}

	
	protected function findPreviousStatus()
	{
		$this->errLoc = "method " . __METHOD__;

		$dosql = "SELECT status FROM " . DB_PREFIX . "lb_postings WHERE id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);
		$result = $GLOBALS['lbdata'] -> executePreparedStatement(array(':id'=>$this->edit_id));
			
		return $result[0]['status'];
	}

	protected function getDate()
	{
		if (!isset($_POST['now']))
		{

			$posted = 	$_POST['post1'] . "-" . $_POST['post2'] . "-" .
						$_POST['post3'] . " " . $_POST['post4'] . ":" .
						$_POST['post5'] . ":00";

		}
		else
		{
			$posted = date("Y-m-d H:i:s");
		}
		return $posted;
	}

	protected function tickbox($box)
	{
		if (isset($_POST[$box]))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	protected function getDuration()
	{
		// if POST value is 'xxxx seconds', remove ' seconds'; else do not change the POST value
		$pieces = explode (" ", $_POST['audio_length']);
		$lengthint = round ($pieces[0]);

		return $lengthint;
		
	}

	protected function getSize()
	{
		if (strpos($_POST['audio_size'], ' ')) // ie if the POST value is 'xxxx MB'
		{
			$pieces = explode (" ", $_POST['audio_size']);
			$sizeint = round ($pieces[0]) * 1024 * 1024;
		}
		elseif (ctype_digit($_POST['audio_size'])) // ie if the POST value is an integer
		{
			$sizeint = $_POST['audio_size'];
		}
		else
		{
			$sizeint = '0';
		}
		return $sizeint;
	}

	protected function updatePreviews()
	{
		$this->errLoc = "method " . __METHOD__;
		
		$dosql = "UPDATE ".DB_PREFIX."lb_settings SET value  = :value WHERE name = 'previews'";
		$GLOBALS['lbdata']->prepareStatement($dosql);
		$GLOBALS['lbdata']->executePreparedStatement(array(':value' => $_POST['previews']));
			
		$this->reg->refreshSettings();		
	}

	protected function updateLinks()
	{
		$this->errLoc = "method " . __METHOD__;

		$success = false;
		$dosql = "DELETE FROM ".DB_PREFIX."lb_links WHERE posting_id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);		

		$success = $GLOBALS['lbdata']->executePreparedStatement(array(':id'=>$this->edit_id));

		$showlinks = $this->reg->findSetting('showlinks');
			
		for ($i = 0; $i< $showlinks; $i++)
		{
			$success = false;

			$temptit = "linktit" . $i;
			$tempurl = "linkurl" . $i;
			$tempdes = "linkdes" . $i;

			$writetit = entity_encode($_POST[$temptit]);
			$writedes = entity_encode($_POST[$tempdes]);
			$writeurl = $_POST[$tempurl];

			if (strrpos($writeurl, "://") == false)
			{
				$writeurl = "http://".$writeurl;
			}

			if ($_POST[$tempurl] != "")
			{
				$dosql = "INSERT INTO ".DB_PREFIX."lb_links
							(posting_id, linkorder, title, url, description)
							VALUES
							(:posting_id, :linkorder, :title, :url, :description)";
				$GLOBALS['lbdata']->prepareStatement($dosql);

				$input_array = array(
						':posting_id' 	=> $this->edit_id,
						':linkorder' 	=> $i,
						':title' 		=> $writetit,
						':url' 			=> $writeurl,
						':description' 	=> $writedes);

				$success = $GLOBALS['lbdata']->executePreparedStatement($input_array);					
			}
		}		
	}

	protected function getAuthorID()
	{
		$dosql = "SELECT author_id FROM " . DB_PREFIX . "lb_postings where id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $this->edit_id));
		return $result[0]['author_id'];	
	}
}
?>
