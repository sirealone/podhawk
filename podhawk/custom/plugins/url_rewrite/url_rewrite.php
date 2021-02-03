<?php

class url_rewrite extends PluginPattern
{
	public function __construct($data=NULL)
	{
		$this->myName = 'url_rewrite';
		$this->myFullName = 'URL Rewrite Module';
		$this->description = 'This module replaces the standard urls for posts, categories etc (ie urls in the form "www.mysite.com/index.php?id=..") with urls which are more readily "human readable" and which may improve the ability of search engines to index your site appropriately.';
		$this->version = '1.0';
		$this->author = 'Peter Carter';
		$this->contact = 'cpetercarter@googlemail.com';
	
		$this->initialParams = array(	'posts' 		=> 0,
										'categories' 	=> 0,
										'tags' 			=> 0,
										'authors' 		=> 0);

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
		$this->enabled = (!empty($data['enabled'])) ? $data['enabled'] : 0;

		$this->listeners = array('onRedirect', 'onPostingDataReady', 'onAllPageDataReady', 'onSavePosting');

		$this->reg = Registry::instance();
	}

	protected function backendPluginsPage()
	{

		$html = <<<EOF
			<tr>
			<td class="left">Rewrite URLs of postings</td>
			<td class="center">
EOF;
		$html .= $this->makeCheckBox(1, 'posts');
		$html .= <<<EOF
			</td>
			<td class="right">Your posts will have URLs (permalinks) which contain the title of the post and its id eg "http://www.mysite.com/12/my-posting-title"</td>
		</tr>
		<tr>
			<td class="left">Rewrite URLs of categories</td>
			<td class="center">
EOF;

		$html .= $this->makeCheckbox(1, 'categories');
		$html .= <<<EOF
			</td>
			<td class="right">Links to your categories will have the form "http://www.mysite.com/category_name"</td>
		</tr>
		<tr>
			<td class="left">Rewrite URLs of tags</td>
			<td class="center">
EOF;
		$html .= $this->makeCheckbox(1, 'tags');
		$html .= <<<EOF
			</td>
			<td class="right">Links to your tags will have the form "http://www.mysite.com/tag_name"</td>
		</tr>
		<tr>
			<td class="left">Rewrite URLs of authors</td>
			<td class="center">
EOF;
		$html .= $this->makeCheckbox(1, 'authors');
		$html .= <<<EOF
			</td>
			<td class="right">Links to your authors will have the form "http://www.mysite.com/author_name"</td>
		</tr>
EOF;

		return $html;
	}

	public function getParamsFromPosts()
	{
		$params = $this->params;

		// $_POST values are returned only from checked checkboxes
		$options = array("posts", "categories", "tags", "authors");
		foreach ($options as $option)
		{
			$params[$option] = (isset($_POST[$option])) ? $_POST[$option] : 0;
		}

		return $params;
	}

	public function setup()
	{
		$ok = true;

		// add the plugin to the plugins table
		$dosql = "INSERT INTO ". DB_PREFIX . "lb_plugins (name, full_name, run_order, enabled, params)
		VALUES (" . escape($this->myName) . ",
				" . escape($this->myFullName) . ",
				'3',
		 		'0',
				 " . escape(serialize($this->initialParams)) . ");";

		if (!$GLOBALS['lbdata']->Execute($dosql)) $ok = false;

		// create the permalinks table
		if (DB_TYPE == 'mysql')
		{
			$dosql = "CREATE TABLE " . DB_PREFIX ."lb_permalinks(
						id INTEGER PRIMARY KEY AUTO_INCREMENT,
						posting_id INTEGER,
						permalink VARCHAR(256))";
		}
		elseif (DB_TYPE == 'postgres7' || DB_TYPE == 'postgres8')
		{
			$dosql = "CREATE TABLE " . DB_PREFIX ."lb_permalinks(
						id SERIAL PRIMARY KEY,
						posting_id INTEGER,
						permalink VARCHAR(256))";
		}
		else // sqlite
		{
			$dosql = "CREATE TABLE ". DB_PREFIX . "lb_permalinks(
						id INTEGER PRIMARY KEY,
						posting_id INTEGER(5),
						permalink VARCHAR(256))";
		}

		if (!$GLOBALS['lbdata']->Execute($dosql)) $ok = false;

		// for postgres, we must give explicit SELECT privileges to the front end user
		if ((DB_TYPE == 'postgres7' || DB_TYPE == 'postgres8') && defined('DB_FE_USER') && DB_FE_USER != '')
		{
			$dosql = "GRANT SELECT on " . DB_PREFIX . "lb_permalinks TO " . DB_FE_USER .";";
			
			if (!$GLOBALS['lbdata']->Execute($dosql)) $ok = false;
		}
			

		return $ok;
	}

	public function remove()
	{
		$ok = true;

		// remove the plugin from the database
		$dosql = "DELETE FROM " . DB_PREFIX . "lb_plugins WHERE name = " . escape($this->myName);
		if (!$GLOBALS['lbdata'] -> Execute ($dosql)) $ok = false;

		// destroy the permalinks table
		$dosql = "DROP TABLE " . DB_PREFIX . "lb_permalinks";

		if (!$GLOBALS['lbdata']->Execute($dosql)) $ok = false;

		return $ok;

	}

	public function writeData()
	{
		// write post data to plugins table
		parent::writeData();

		// empty the permalinks table
		$dosql = "DELETE FROM " . DB_PREFIX . "lb_permalinks";
		$GLOBALS['lbdata']->Execute($dosql);

		// if 'posting' checkbox is selected and the plugin is enabled, insert data in permalinks table
		if (!empty($_POST['posts']) && !empty($_POST['enable']))
		{
			$dosql = "SELECT id, title FROM " . DB_PREFIX . "lb_postings";
			$results = $GLOBALS['lbdata']->GetArray($dosql);

			if (!empty($results))
			{
				foreach ($results as $result)
				{

					$permalink = $this->makePermalink($result);

					$dosql = "INSERT INTO " . DB_PREFIX . "lb_permalinks (
								posting_id, permalink)
								VALUES
								('" . $result['id'] . "', " . $permalink . ")";
					$GLOBALS['lbdata']->Execute($dosql);
				}
			}
		}
	}
	
	private function makePermalink($result)
	{
		//decode the html-encoded title, and convert to lower case
		$title = strtolower(my_html_entity_decode($result['title']));
		
		include PLUGINS_DIR . $this->myName . '/replaceCharacters.php';

		//replace non-permitted characters with permitted characters, and spaces with hyphens
		$title = str_replace($keys, $values, $title);

		//remove any non-alphanumeric characters apart from hyphens
		$title = preg_replace("/[^A-Za-z0-9-_\.]/", "", $title); 

		//urlencode any remaining non-permitted characters
		$title = rawurlencode($title);

		//build the permalink, and 'escape' so that it can safely be placed in the database
		$permalink =  $result['id'] . '-' . $title;
		$permalink = escape($permalink);

		return $permalink;
	}	
		
	public function onRedirect($requested)
	{
		// is the first part of $requested a number? If it is, then it is the id of a posting which we want
		$return = array();
		$bits = explode('-', $requested);

		if (ctype_digit($bits[0]))
		{
			$_GET['id'] = $bits[0];
		}
	}

	public function onPostingDataReady($postings)
	{
		global $posting_categories, $posting_tag_links;

		if ($this->params['categories'] == 1)
		{
			foreach ($posting_categories as $key => $categories)
			{
				foreach ($categories as $i => $category)
				{
					$name = rawurlencode(my_html_entity_decode($category['name']));
					$posting_categories[$key][$i]['link'] = THIS_URL . '/' . $name;
				}
			}
		}

		if ($this->params['tags'] == 1)
		{
			foreach ($posting_tag_links as $key => $taglink)
			{
				foreach ($taglink as $tag => $link)
				{
					$name = rawurlencode(my_html_entity_decode($tag));
					$posting_tag_links[$key][$tag] = THIS_URL . '/' . $name;
				}
			}
		}

	}

	public function onAllPageDataReady()
	{
		global $tag_links, $categories, $authors;

		if ($this->params['categories'] == 1)
		{
			foreach ($categories as $id => $category)
			{
				$categories[$id]['link'] = THIS_URL . '/' . rawurlencode(my_html_entity_decode($category['name']));
			}
		}

		if ($this->params['tags'] == 1)
		{
			foreach ($tag_links as $tag => $link)
			{
				$tag_links[$tag] = THIS_URL . '/' . rawurlencode(my_html_entity_decode($tag));
			}
		}

		if ($this->params['authors'] == 1)
		{
			foreach ($authors as $id => $details)
			{
				$authors[$id]['link'] = THIS_URL . '/' . rawurlencode(my_html_entity_decode($details['nickname']));
			}
		}
		
	}

	public function onSavePosting($id)
	{
		$dosql = "DELETE FROM " . DB_PREFIX . "lb_permalinks WHERE posting_id = " . $id;
		$GLOBALS['lbdata'] -> Execute($dosql);

		$a = array('title' 	=> $_POST['title'],
					'id' 	=> $id);

		$permalink = $this->makePermalink($a);

		$dosql = "INSERT INTO " . DB_PREFIX . "lb_permalinks (
								posting_id, permalink)
								VALUES
								('" . $id . "', " . $permalink . ")";
		$GLOBALS['lbdata']->Execute($dosql);
	}				
}
?>
