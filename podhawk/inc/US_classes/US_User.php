<?php

class US_User
{

	private $user_id;
	private $reg;
	private $user; // data about user as an object

	public function __construct($id)
	{
		$this->user_id = $id;

		$this->reg = Registry::instance();

		$this->user = $this->reg->getAuthor($id);
	}

	public function isMe($id)
	{
		return ($id == $this->user_id);
	}

	public function getID()
	{
		return $this->user_id;
	}

	public function isAdmin()
	{

		return $this->user['admin'];
	}

	public function isOwner($author_id)
	{
		return ($this->user_id == $author_id);
	}

	public function mayEdit($author_id)
	{
		if ($this->isAdmin()) return true;
		
		elseif ($this->user['edit_all'] == true) return true;
		
		elseif ($this->user['edit_own'] == true && $this->user_id == $author_id) return true;
		
		else return false;
	}

	public function mayPublish($author_id)
	{
		if ($this->isAdmin()) return true;
		
		elseif ($this->user['publish_all'] == true) return true;
		
		elseif ($this->user['publish_own'] == true && $this->user_id == $author_id) return true;
		
		else return false;
	}

	public function mayEditAll()
	{
		if ($this->isAdmin()) return true;

		elseif ($this->user['edit_all'] == true) return true;

		else return false;
	}

	public function mayEditComment($commentID, $table = 'lb_comments')
	{
		// we search comments table by default, but can search spam table by passing the appropriate $table parameter
		$dosql = "SELECT author_id FROM ". DB_PREFIX . "lb_postings WHERE id = (SELECT posting_id FROM " 
						. DB_PREFIX . "$table WHERE id = :id)";

		$GLOBALS['lbdata'] -> prepareStatement($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $commentID));

		return ($this->mayEdit($result[0]['author_id']));
	}

	public function getNickname()
	{
		return $this->user['nickname'];
	}

	public function getLoginName()
	{
		$dosql = "SELECT login_name FROM " . DB_PREFIX . "lb_authors WHERE id = :id";
		$GLOBALS['lbdata']->prepareStatement($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $this->user_id));
		return $result[0]['login_name'];
	}
}
?>
