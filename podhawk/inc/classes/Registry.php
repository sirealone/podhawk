<?php

class Registry  {

// this class holds data about settings, categories and players taken from the database. The instance property contains
// a static instance of the class which can be used globally. The only public methods are READ ONLY. 

	private static $instance;
	private $settings;  // array of all settings arrays
	private $categories;  // array of all categories arrays
	private $players; // array of all players arrays
	private $authors; // array of all authors arrays
	private $encryptedSettings = array('ftp_user', 'ftp_pass', 'amazon_access', 'amazon_secret');
	private $caching = true;

	private function __construct()
	{
		$this->getSettingsFromDB();				
		$this->getCategoriesFromDB();				
		$this->getPlayersFromDB();				
		$this->getAuthorsFromDB();
	}

	public function __destruct()
	{
		if (PH_CACHING == true && $this->caching == true)
		{
			$c = new DA_Cache('registry');
			$c->writeToCache($this);
		}
	}

	private function __clone()
	{
	
	}

	static function instance()
	{
		if (!isset (self::$instance))
		{
			if (file_exists(PH_CACHE . 'registry') && PH_CACHING == true)
			{
				$c = new DA_Cache('registry');
				self::$instance = $c->getFromCache();

				// test that we do not have a corrupted cache copy
				if (empty(self::$instance->settings['settingsArray']) ||
					empty(self::$instance->categories['categoriesArray']) ||
					empty(self::$instance->players['playersArray']) ||
					empty(self::$instance->authors['authorsArray']))
				{
					self::$instance = new self();
				}
			}
			else
			{
				self::$instance = new self();
			}			
		}
		return self::$instance;		
	}

	// when we clear the PH cache, we need to force a rebuild of the Registry
	public static function forceRebuild()
	{
		return new self();
	}

	public function refreshAll()
	{
		$this->refreshSettings();
		$this->refreshCategories();
		$this->refreshPlayers();
		$this->refreshAuthors();
	}

	public function blockCaching()
	{
		$this->caching = false;
	}

	## Settings ##

	public function getSettingsArray()
	// returns an associative array (name => value) of settings
	{
		return $this->settings['settingsArray'];
	}

	public function getSanitisedSettings()
	// returns associative (name=>value) array of settings with sensitive data removed
	// this should be used for settings which are sent to Smarty templates
	{
		if (empty($this->settings['sanitisedSettings']))
		{
			$settings = $this->settings['settingsArray'];
		
			unset (	$settings['ftp_user'],
					$settings['ftp_pass'],
					$settings['ftp_server'],
					$settings['ftp_path'],
					$settings['amazon_access'],
					$settings['amazon_secret'],
					$settings['error_reporting'],
					$settings['fb_api_key'],
					$settings['fb_secret'],
					$settings['spamanswer'],
					$settings['akismet_key']);

			$this->settings['sanitisedSettings'] = $settings;
		}

		return $this->settings['sanitisedSettings'];
	}

	public function findSetting($name)
	// find a setting value from setting name
	{
		if (in_array($name, $this->encryptedSettings))
		{
			$return = $this->decrypt($name);
		}
		else
		{
			$return = (isset($this->settings['settingsArray'][$name])) ? $this->settings['settingsArray'][$name] : false;
		}
		return $return;
	}

	public function refreshSettings()
	// refreshes the Registry's data about settings  
	{
		unset ($this->settings);
		$this->getSettingsFromDB();
		return $this->settings['settingsArray'];
	}

	public function getFeedAddress()
	{
		if (empty($this->settings['feed']))
		{
			$this->getFeed();
		}

		return $this->settings['feed'];
	}

	public function getCommentFeedAddress()
	{
		if (empty($this->settings['commentfeed']))
		{
			$this->getFeed();
		}

		return $this->settings['commentfeed'];
	}

	private function getFeed()
	// finds the appropriate address of the rss feed and of the comments feed
	{
		$this->settings['feed'] = THIS_URL . "/podcast.php";
		$this->settings['commentfeed'] = $this->settings['feed'] . "?com=1";

		$alt = $this->settings['settingsArray']['alternate_feed_address'];

		if (!empty($alt) && trim($alt) != "http://")
		{
			$this->settings['feed'] = $alt;
		}
			
	}

	public function ftpSettingsAvailable() // have we a full set of FTP settings?
	{
		return 	(!empty($this->settings['settingsArray']['ftp_server'])
				&& !empty($this->settings['settingsArray']['ftp_path'])
				&& !empty($this->settings['settingsArray']['ftp_user'])
				&& !empty($this->settings['settingsArray']['ftp_pass']));
	}

	public function amazonAvailable() // have we a full set of Amazon S3 data?
	{
		return  ($this->settings['settingsArray']['amazon'] == 1
				&& !empty ($this->settings['settingsArray']['amazon_access'])
				&& !empty ($this->settings['settingsArray']['amazon_secret'])
				&& !empty ($this->settings['settingsArray']['amazon_bucket'])
				);
	}

	public function setEncryptedSetting($name) // add a new setting to the encrypted array
	{
		if (isset($this->settings['settingsArray'][$name]))
		{
			$this->encryptedSettings[] = $name;
		}
	}

	## Categories ##

	public function getCategoriesArray()
	// returns an indexed array of categories data (
	{
		return $this->categories['categoriesArray'];
	}

	public function getCategoryNames()
	// returns an array of category names indexed by category id
	{
		if (empty($this->categories['categoryNames']))
		{		
			foreach ($this->categories['categoriesArray'] as $cat)
			{
				$this->categories['categoryNames'][$cat['id']] = $cat['name'];
			}
		}
		return $this->categories['categoryNames'];
	}	

	public function getCategory($id)
	// returns the name of a category from its id
	{		
		return $this->categories['categoriesByID'][$id]['name'];
	}

	public function getURLEncodedCategoryName($id)
	// returns the name of a category encoded for use in a URL
	{
		$name = $this->getCategory($id);
		
		return rawurlencode(my_html_entity_decode($name));
	}

	public function getCategoryId($name)
	// returns the id of a category from its name
	{
		$id = 0; // 0 = no category

		// the category name in the DB is encoded. The name we have been given may or may not be encoded. So...
		$catNameArray = array ($name, entity_encode($name), htmlentities($name, ENT_QUOTES, "UTF-8"));

		$catsdump = $this->getCategoriesArray();

		foreach ($catsdump as $cat)
		{
			// spaces in $_GETs are removed by killevilcharacters()
			$noSpaceName = str_replace(" ","",$cat['name']);

			if (in_array($cat['name'], $catNameArray) || in_array($noSpaceName, $catNameArray))
			{
				$id = $cat['id'];
				break;
			}
		}
		return $id;
	}
				
	
	public function refreshCategories()
	// refreshes the Registry's categories data
	{
		unset ($this->categories);
		$this->getCategoriesFromDB();
		return $this->categories['categoriesArray'];
	}

	## Players ##

	public function getPlayers()
	//returns associative array (name=>value) of players data
	{
		return $this->players['playersArray'];
	}

	public function refreshPlayers()
	// refreshes the Registry's players data
	{
		unset ($this->players);
		$this->getPlayersFromDB();
		return $this->players['playersArray'];
	}

	public function findPlayersSetting($setting)
	{
		return $this->players['playersArray'][$setting];
	}

	## Authors and User Rights ##
	
	public function getAuthors()
	{
		return $this->authors['authorsArray'];
	}

	public function refreshAuthors()
	{
		unset ($this->authors);
		$this->getAuthorsFromDB();
		return $this->authors['authorsArray'];
	}

	public function getNickname($authorId)
	// returns author nickname (screen name) from author id
	{
		if (empty($this->authors['authorsArray'][$authorId]['nickname']))
		{
			return false;
		}
		else
		{
			return $this->authors['authorsArray'][$authorId]['nickname'];
		}		
	}

	public function getRealName($authorId)
	// returns author real name from author id
	{
		return $this->authors['authorsArray'][$authorId]['realname'];
	}

	public static function getIdFromLogin($name)
	// gets the author id from the author's login name
	{
		$dosql = "SELECT id FROM " . DB_PREFIX . "lb_authors WHERE login_name = '$name'";
		$result = $GLOBALS['lbdata']->getArray($dosql);
		return $result[0]['id'];
	}

	public static function getLoginName($id)
	// returns the login name of the logged in user
	{
		$dosql = "SELECT login_name FROM " . DB_PREFIX . "lb_authors WHERE id = $id";
		$result = $GLOBALS['lbdata']->getArray($dosql);
		return $result[0]['login_name'];
	}

	public function getAuthorsList()
	// returns array key = author id, value = array (nickname, mail, link, hide)
	{
		if (empty($this->authors['authorsList']))
		{
			$list = array();
			foreach ($this->authors['authorsArray'] as $id => $details)
			{
				$list[$id] = array(	'nickname' => $details['nickname'],
									'mail' => $details['mail'],
									'link' => THIS_URL . '/index.php?author=' . $id,
									'hide' => $details['hide']);
			}
			$this->authors['authorsList'] = $list;
		}
		return $this->authors['authorsList'];
	}

	public function getAuthorNicknames()
	// returns array of all author nicknames
	{	
		if (empty($this->authors['authorNicknames']))
		{
			$list = array();
			foreach ($this->authors['authorsArray'] as $author)
			{
				$list[] = $author['nickname'];
			}

			$this->authors['authorNicknames'] = $list;
		}

		return $this->authors['authorNicknames'];
	}

	public function getAuthorNicknamesIDIndex()
	// returns array author_id => nickname
	{
		if (empty($this->authors['authorNicknamesIDIndex']))
		{
			$list = array();
			foreach ($this->authors['authorsArray'] as $author)
			{
				$list[$author['id']] = $author['nickname'];
			}
		
			$this->authors['authorNicknamesIDIndex'] = $list;
		}
		return $this->authors['authorNicknamesIDIndex'];
	}

	public function getAuthorIDFromNickname($nickname)
	{
		$array = $this->getAuthorNicknamesIDIndex();
		$array = array_flip($array);
		if (isset($array[$nickname]))
		{
			return $array[$nickname];
		}
		else
		{
			return FALSE;
		}
	}

	public function getAuthor($id)
	{
		return $this->authors['authorsArray'][$id];
	}

	## Private Functions ##
	
	private function getSettingsFromDB()
	{
		$dosql = "SELECT * FROM " .DB_PREFIX. "lb_settings;";
		$array = $GLOBALS['lbdata']->GetAssoc($dosql);
		
		$this->settings['settingsArray'] = $array;
	}

	private function getCategoriesFromDB()
	{

		$dosql = "SELECT * FROM " .DB_PREFIX. "lb_categories ORDER BY name ASC;";
		$array = $GLOBALS['lbdata']->GetArray($dosql);

		//categoriesArray is indexed
		$this->categories['categoriesArray'] = $array;
		
		//but the $categoriesByID is an associative array, indexed on category id
		foreach ($array as $a)
		{	
			$this->categories['categoriesByID'][$a['id']]['name'] 			= $a['name'];
			$this->categories['categoriesByID'][$a['id']]['description'] 	= $a['description'];				
		}
	}

	private function getPlayersFromDB()
	{

		$dosql = "SELECT * FROM " .DB_PREFIX. "lb_players;";
		$array = $GLOBALS['lbdata']->GetAssoc($dosql);
		
		//add dimensions of emff and loudblog players
		$emff_dimensions = $this->emffDimensions($array['emff_player']);
		$array['emff_height'] = $emff_dimensions['height'];
		$array['emff_width'] = $emff_dimensions['width'];

		$loudblog_dimensions = $this->loudblogDimensions($this->settings['settingsArray']['template']);
		$array['loudblog_height'] = $loudblog_dimensions['height'];
		$array['loudblog_width'] = $loudblog_dimensions['width'];

		$this->players['playersArray'] = $array;
		
	}

	
	private function getAuthorsFromDB()
	// retrieves non-sensitive author data from database (ie not login name or password)
	{
		$dosql = "SELECT * FROM " . DB_PREFIX . "lb_authors";
		$result = $GLOBALS['lbdata']->GetArray($dosql);
	
		foreach ($result as $row)
		{
			foreach ($row as $field => $value)
			{
				// exclude login_name and password
				if ($field == 'login_name' || $field == 'password') continue;
				// array indexed on author id
				$this->authors['authorsArray'][$row['id']][$field] = $value;
			}
		}
	}

	private function decrypt($name)
	{
		$the_setting = $this->settings['settingsArray'][$name];

		if (defined('BLOWFISH_KEY') && USE_BLOWFISH_ENCRYPTION == true)
		{
			$blowfish = new SE_Blowfish;
			$the_setting = $blowfish->decrypt($the_setting);
		}
		return $the_setting;
	}
	
	## Static functions ##

	static function emffDimensions($player)
	{
		 switch ($player)
		{
			case 'easy_glaze'  :
			$h = 32;
			$w = 32;
			break;
			case 'easy_glaze_small'  :
			$h = 22;
			$w = 22;
			break;
			case 'lila'  :
			$h = 55;
			$w = 200;
			break;
			case 'lila_info'  :
			$h = 55;
			$w = 200;
			break;
			case 'old'  :
			$h = 55;
			$w = 120;
			break;
			case 'old_noborder'  :
			$h = 25;
			$w = 91;
			break;
			case 'position_blue'  :
			$h = 50;
			$w = 100;
			break;
			case 'silk'  :
			$h = 32;
			$w = 84;
			break;
			case 'silk_button'  :
			$h = 16;
			$w = 16;
			break;
			case 'standard'  :
			$h = 34;
			$w = 110;
			break;
			case 'stuttgart'  :
			$h = 30;
			$w = 140;
			break;
			case 'wooden'  :
			$h = 60;
			$w = 120;
			break;
			default :
			$h = 60;
			$w = 60;
			break;
		}
	$return = array('height' => $h, 'width' => $w);

	return $return;
	}

	static function loudblogDimensions ($theme)
	{		
		switch ($theme)
		{
			case 'cleaker' :
				$h = 20;
				$w = 150;
				break;
			case 'default' :
				$h = 70;
				$w = 200;
				break;
			case 'kubrick' :
				$h = 28;
				$w = 255;
				break;
			case 'redtrain' :
				$h = 60;
				$w = 121;
				break;
	
			case 'aalglatt' :
				$h = 20;
				$w = 150;
				break;
			case 'digg' :
				$h = 20;
				$w= 150;
				break;
			case 'zigzag' :
				$h = 20;
				$w = 150;
				break;	
			default :
				$h = 20;
				$w = 150;
				break;
		}
	
	$return = array('height' => $h, 'width' => $w);

	return $return;
	} 
		
}
?>
