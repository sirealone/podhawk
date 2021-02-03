<?php

/**
*
* The Comment Notifier class sends an email to the post author when a comment is posted on one of his/her posts
*
* Arguments in the constructor are the post id, and the name, email address, website and message of the commenter
*
*/

class MA_CommentNotifier extends MA_GeekMail {

	private $post_author_name;
	private $post_author_email;
	private $post_id;
	private $post_title;
	private $commenter_name;
	private $comment_email;
	private $comment_web;
	private $comment_text;

	public function __construct ($id, $name, $mail, $web, $message) {

		parent::__construct();

		$this->post_id = $id;
		$this->commenter_name = $name;
		$this->comment_email = $mail;
		$this->comment_web = $web;
		$this->comment_text = $message;

	}

	private function getPostAndAuthorDetails($id)  {

	$dosql = "SELECT author_id, title FROM ".DB_PREFIX."lb_postings WHERE id = $id";
	$posting = $GLOBALS['lbdata']->getarray($dosql);

	$this->post_title = $posting[0]['title'];

	$authorid = $posting[0]['author_id'];

	$dosql = "SELECT nickname, realname, mail FROM " . DB_PREFIX . "lb_authors WHERE id = '$authorid'";
	$author = $GLOBALS['lbdata']->getarray($dosql);

	$this->post_author_name = (!empty($author[0]['realname'])) ? $author[0]['realname'] : $author[0]['nickname'];
	$this->post_author_email = (!empty($author[0]['mail'])) ? $author[0]['mail'] : '';

	}

	private function prepare($text) {

	if (stripos($text, "Content-Type:")) return "";

	$text = entities_to_chars(strip_tags($text, '<p><b><br />'));

	return $text;

	}

	private function buildMessage ()  {

	$p = new PO_Permalink($this->post_id);
	$url = $p->permalink();
	
	$message = $this->prepare($this->comment_text);

	$name = (empty($this->commenter_name)) ? 'anonymous' : $this->prepare($this->commenter_name);
	$email = (empty($this->comment_email)) ? 'no email' : $this->prepare($this->comment_email);
	$web = (empty($this->comment_web)) ? 'no web address' : $this->prepare($this->comment_web);
	
	$message =<<<EOF
	<p>Hi {$this->post_author_name}</p>
	<p>$name has left a comment on {$this->post_title} $url</p>
	<blockquote>
	$message
	</blockquote>
	<p>Name : $name<br /> 
	Email : $email<br />
	Web : $web<br />
EOF;

	return array('name' => $name,'email' =>  $email,'message' => $message);

	}

	public function sendCommentEmail ()  {

	$this->getPostAndAuthorDetails($this->post_id);

		if (empty($this->post_author_email)) return;

	$this->setMailType('html');

	$message = $this->buildMessage();

	$this->from($message['email'], $message['name']);

	$this->to($this->post_author_email);

	$this->subject('New Comment on ' . SITENAME);

	$this->message($message['message']);

	$this->send();

//		$errors = $this->getDebugger();
//		print_r ($errors);
	
	}
}
?>
