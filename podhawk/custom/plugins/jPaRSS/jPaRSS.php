<?php

class jPaRSS extends PluginPattern
{

	public function __construct ($data=NULL)
	{
		$this->myName = 'jPaRSS';
		$this->myFullName = 'jPaRSS RSS Feed Plugin';
		$this->description = <<<EOF
This Plugin allows you to display RSS feeds - as many as you want - on your PodHawk webpage. It works by loading a javascript-based RSS feed reader to the head section of the page. You can then add the following to your sidebar template where you want the feed items to appear:</p>
<p><code>{include file="plugins:jPaRSS/rss_reader.tpl" feedURL='http://url/of/feed' id='feed1' number='5' description='true'}</code></p>
<p>The parameters you can pass are:</p>
<ul>
<li>url (required) - the url of the RSS feed you want to display eg <code>http://www.my_podhawk_site.com/podcast.php</code></li>
<li>id (required) - a unique id for this feed. It can be almost anything you want, but to avoid possible conflicts with other elements on your page, it is recommended that you use 'feed1' as the id for the first feed you want to show, 'feed2' for the second, and so on.</li>
<li>number (optional) - the number of feed items you want to display. If you omit this parameter, the plugin will display the 4 most recent items.</li>
<li>description (optional) - <ul><li><code>description='true'</code> will display the first part of the text of each feed item.</li><li> <code>description='content'</code> will display the full text of each feed item.</li><li><code>description='image'</code> will display the first image associated with each feed item, plus a short section of the text</li><li><code>description='false'</code> or omitting the 'description' parameter, will display no text.</li></ul></li>
<li>show_date (optional) - <code>show_date='true'</code> displays the date of each feed item in the 'preferred date format' which you set on the backend settings page. <code>show_date='false'</code> or omitting the date parameter will display no date.</li>
</ul>
<p>This plugin requires PodHawk 1.8 or later.
EOF;

	$this->version = "1.0";
	$this->author = "Peter Carter";
	$this->contact = "cpetercarter@googlemail.com";
	
	$this->initial_params = array();

	$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;

	$this->enabled = $data['enabled'];

	$this->listeners = array('onAllPageDataReady', 'addCSS');

	}

	// there are no user defined parameters for this plugin..
	protected function backendPluginsPage()
	{

		return $this->noUserParams();

	}

	//...and therefore no $_POST variables to convert into parameters
	protected function getParamsFromPosts()
	{

		return array();

	}

	public function onAllPageDataReady()
	{
		$return = array();

		if (ACTION == 'webpage')
		{
			// add jparrs.js to the array of js files to load
			$return[] = array(	'plugin' 	=> $this->myName,
								'variable' 	=> 'javascript',
								'offset' 	=> array("jPaRSS"),
								'value' 	=> "podhawk/custom/plugins/jPaRSS/jquery.parss.js");

			// add a variable to hold the preferred date format in php notation
			$return[] = array( 	'plugin' 	=> $this->myName,
								'variable'	=> 'pluginsPageElements',
								'offset'	=> array('date_format_php'),
								'value'		=> $this->getDateFormat());
		}
		return $return;
	}

	public function addCSS()
	{
		$css = array();
		
		if (ACTION == 'webpage')
		{
			$css['jparss'] = "<link rel=\"stylesheet\" media=\"screen\" href=\"podhawk/custom/plugins/jPaRSS/jparss.css\" type=\"text/css\" />";
		}
		return $css;
	}

	private function getDateFormat()
	{
		$reg = Registry::instance();

		$smarty_date_format = $reg->findSetting('preferred_date_format');

		$search = array("%", "a", "A", "b", "B", "e");
		$replace = array("", "D", "l", "M", "F", "j");
		return str_replace($search, $replace, $smarty_date_format);

	}
}
?>
