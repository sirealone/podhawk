<?php

class Rewriter
{
	protected $requested; // the basename of the requested URL
	protected $requestEncoded; // HTML-encoded version of $requested
	protected $reg; // registry instance

	public function __construct ($requested)
	{
		$this->requested = urldecode($requested); // decode any %xx characters in the request

		$this->requestEncoded = entity_encode($this->requested);

		$this->reg = Registry::instance();
	}

	public function rewrite()
	{
		if (ctype_digit($this->requested))
		{
			$_GET['id'] = $this->requested;
		}
	
		else //does it match a category name?
		{		
			if (in_array($this->requestEncoded, whitelist('cats')))
			{
				$_GET['cat'] = $this->requested;
			}
			
			else // does it match the name of an author?
			{		
				if (in_array($this->requestEncoded, whitelist('author_names')))
				{
					$_GET['author'] = $this->reg->getAuthorIDFromNickname($this->requestEncoded);
				}
	
				else //does it match a tag name?
				{
					if (in_array($this->requestEncoded, whitelist('tags')))
					{
						$_GET['tag'] = $this->requested;
					}		
		
					else //is it the name of a template (ending in "_atp")				
					{
						$templates = get_dir_contents("podhawk/custom/themes/".$this->reg->findSetting('template'));
						foreach ($templates as $template)
						{						
							if (substr($template, -8) == "_atp.tpl" && $this->requested == substr($template,0,-8))
							{
								$_GET['atp'] = $this->requested."_atp";
							}
						}
					}
				}
			}						
		}

		if (!isset($_GET))
		{
			$rss_array = array('subscribe', 'rss', 'feed');
			foreach ($rss_array as $item)
			{
				if (stripos($this->requested, $item) !== FALSE)
				{
					$feed = $this->reg->getFeedAddress();
					header("Location: " . $feed);
					exit;
				}
			}
		$this->additionalRules();
		}
	}

	protected function additionalRules()
	{
		// child classes can use this function to add further rewrite rules
	}
}
?>
