<?php

class TagManager
{

	private static $instance;
	private $tags = array(); //array of all tags (unsorted, on air postings only)
	private $weighted_tags = array(); // array of tags, key = tag name, value = number of occurences (unsorted, on-air postings only)
	private $topTagsList; //array of the names of the most frequent on-air tags
	private $topTagsWeights; // array of weights for $topTagsList
	private $tagsForcategory; // array of tags associated with categories

	private function __construct()
	{
		
	}

	private function __clone()
	{
	
	}

	public function __destruct()
	{
		if (PH_CACHING == true)
		{
			$c = new DA_Cache('tagmanager');
			$c->writeToCache($this);
		}
	}

	static public function instance()
	{
		if (!isset (self::$instance))
		{
			if (file_exists(PH_CACHE . 'tagmanager') && PH_CACHING == true)
			{
				$c = new DA_Cache('tagmanager');
				self::$instance = $c->getFromCache();
			}
			else
			{
				self::$instance = new self();
			}			
		}
		return self::$instance;			
	}

	public function getSortedTagList()
	// returns a list of tags quasi-alphabetically sorted 
	{
		if (empty($this->tags))
		{
			$this->findAllOnAirTags();
		}

		$return = $this->tags;
		natcasesort ($return);
		return $return;
	}

	public function getAllTagsList()
	// returns unsorted list of all (on-air and not yet on-air) tags
	{
		$tag_list = $this->findAllTags();

		return array_keys($tag_list);
	}

	public function getTagCloudWeights($cloud=array())
	//returns an array with tag names as key, and weight (1 to 5) as value.
	//$cloud = array of tag names to include in the cloud - default is all tag names.
	{
		if (empty($this->weighted_tags)) 
		{
			$this->findAllOnAirTags();
		}

		$cloud = (!empty($cloud)) ? $cloud : $this->tags;

		$tagweights = array();

		foreach ($cloud as $tag)
		{

		    $f = 5;
		    $t_i = $this->weighted_tags[$tag];
		    $t_min = 1;
		    $t_max = max($this->weighted_tags);

		    $s_i = $t_i > $t_min ? (  ($f * ($t_i-$t_min) )  /  ($t_max-$t_min)   ) : $t_min;

		    $tagweights[$tag] =(int) ceil($s_i);		  
        }

		return $tagweights;
	}

	public function getTagLinks()
	// returns array (key = tag name, value = link to postings with that tag)
	{
		if (empty($this->tags))
		{
			$this->findAllOnAirTags();
		}
		
		$taglinks = array();

		foreach ($this->tags as $tag)
		{
			$taglinks[$tag] = 'index.php?tag=' . rawurlencode(my_html_entity_decode($tag));
		}
		
		return $taglinks;
	}

	

	public function getTopTagsList($number=10)
	{
		// returns a numerically indexed array of the $number most frequent on air tags, ordered (more or less) alphabetically
		if (empty($this->topTagsList[$number]))
		{
			$topTags = $this->getTopTags($number);
			$list = array_keys($topTags);
			natcasesort($list);
			$this->topTagsList[$number] = $list;
		} 
		return $this->topTagsList[$number];
	}

	public function getTopTagsWeights($number=10)
		// returns an array (tag name => weight) of the $number most frequent on air tags
	{
		if (empty($this->topTagsWeights[$number]))
		{
			if (empty($this->topTagsList))
			{
				$this->getTopTagsList($number);
			}
			$this->topTagsWeights[$number] = $this->getTagCloudWeights($this->topTagsList[$number]);
		}
		return $this->topTagsWeights[$number];
	}

	public function getTagsForCategory($id)
	// returns array of names of tags in on-air posts with category id $id	
	{
		if (empty($this->tagsForCategory[$id]))
		{
			$dosql = "SELECT tags FROM " . DB_PREFIX . "lb_postings	WHERE status = 3 AND (category1_id = :id1 OR category2_id = :id2 OR category3_id = :id3 OR category4_id = :id4)";

			$GLOBALS['lbdata']->prepareStatement($dosql);

			$preparedStatementArray = array(':id1' => $id,
											':id2' => $id,
											':id3' => $id,
											':id4' => $id);

			$result = $GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);

			$tags_array = $this->extractTagArray($result);

			$this->tagsForCategory[$id] = array_keys($tags_array);
		}
		return $this->tagsForCategory[$id];
	}	

	private function findAllOnAirTags()
	{
		$dosql = "SELECT tags FROM " . DB_PREFIX . "lb_postings WHERE status = 3";
		$result = $GLOBALS['lbdata']->getArray($dosql);
		
		$tags_array = $this->extractTagArray($result);

		$this->tags = array_keys($tags_array);		

		$this->weighted_tags = $tags_array;		
		
	}

	private function findAllTags()
	{
	// returns an array (key = tag name, value = no of occurences) of all tags including from postings not yet on air
		$dosql = "SELECT tags FROM " . DB_PREFIX . "lb_postings";
		$result = $GLOBALS['lbdata']->getArray($dosql);

		$tags_array = $this->extractTagArray($result);

		return $tags_array;
	}

	private function extractTagArray ($result)
	//creates an array (tagname => no of occurences) from database result 
	{
		$tags_array = array();

		foreach ($result as $tagrow)
		{
			$tagrow = $tagrow['tags'];
			$tags_tmp = explode(' ', $tagrow);
			foreach ($tags_tmp as $tag_add)
			{
				if ($tag_add == '') continue;
				if (isset($tags_array[$tag_add])) $tags_array[$tag_add] ++;
				else $tags_array[$tag_add] = 1;
			}
		}

		return $tags_array;
	}

	private function getTopTags($number=10)
	// returns array (key = tag name, value = no of occurences) of the $number most common tags (ie those with the highest number of occurences)
	{
		if (empty($this->weighted_tags))
		{
			$this->findAllOnAirTags();
		}

		$topTags = $this->weighted_tags;
		arsort($topTags, SORT_NUMERIC);
		while (count($topTags) > $number)
		{
			array_pop($topTags);
		}

		return $topTags;
	}
}
?>
