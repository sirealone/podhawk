<?php

class CookieMaker
{

	private $user_agent;
	private $length;
	private $cookie;
	private $life;


	public function __construct()
	{
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$this->length = 16;
		$this->cookie = (isset($_COOKIE['phauth'])) ? $_COOKIE['phauth'] : "";
		$this->life = 60*60*24*7; // 1 week
	}

	public function makeCookie($id)
	{

		$identifier = $this->makeIdentifier();
		$this->setCookie($identifier);
		$user_agent = escape($this->user_agent);
		$dosql = "INSERT INTO ".DB_PREFIX."lb_cookies VALUES ('".$identifier."', '".$id."', '".time()."', ".$user_agent.");";

		if($GLOBALS['lbdata']->Execute($dosql))
		{
			
			return true;
		}
		else
		{
			return false;
		}
	}

	public function regenerateCookie($id)
	{

		$cookie = escape($this->cookie);
		$dosql = "DELETE FROM ".DB_PREFIX."lb_cookies WHERE identifier = " . $cookie . ";";
		$GLOBALS['lbdata']->Execute($dosql);
		$this->makeCookie($id);
	}

	public function checkCookie()
	{
	
		$this->removeOldCookies();
		if ($this->sanitiseCookie($this->cookie) == false) return false;
	
		$cookie = escape($this->cookie);
		$dosql = "SELECT * FROM ".DB_PREFIX."lb_cookies WHERE identifier = ".$cookie.";";

		$result = $GLOBALS['lbdata'] -> GetArray($dosql);

		if (empty($result)) return false;
		if ($result[0]['user_agent'] != $this->user_agent) return false;
		if (!isset($result[0]['id'])) return false;

		$id = $result[0]['id'];
		return $id;
	
	}

	public function makeIdentifier ()
	{

		return generatePassword($this->length);

	}

	public function setCookie ($value)
	{

		setcookie('phauth', $value, time() + $this->life);

	}

	public function destroyCookie ()
	{

		if (isset($_COOKIE['phauth']))
		{
			$cookie = escape($this->cookie);
			$dosql = "DELETE FROM ".DB_PREFIX."lb_cookies WHERE identifier = ".$cookie.";";
			$GLOBALS['lbdata'] -> Execute($dosql);
			setcookie('phauth', '', time()-3600);
			$_COOKIE['phauth'] = "";
		}
	}	

	public function clearCookies ()
	{

		$dosql = "DELETE FROM ".DB_PREFIX."lb_cookies;";
		$GLOBALS['lbdata'] -> Execute($dosql);

	}

	private function removeOldCookies ()
	{	
		$dosql = "DELETE FROM ".DB_PREFIX."lb_cookies WHERE time < '". (time() - $this->life) ."';";
		$GLOBALS['lbdata']->Execute($dosql);
	}

	private function sanitiseCookie ($cookie)
	{

		$preg = "/^[a-zA-Z0-9]{".$this->length."}$/";
		return preg_match($preg, $cookie);

	}
}

?>
