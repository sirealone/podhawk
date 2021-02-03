<?php

class share_this extends PluginPattern  {

	private $available_icons = array();
	private $icon_options = array();
	private $text_options = array();

function __construct($data=NULL)  {

	$this->myName = "share_this";
	$this->myFullName = "Share This";
	$this->version = "1.1";
	$this->description = "This plugin will place a \"Share This\" link <img src=\"" . THIS_URL . "/podhawk/custom/plugins/share_this/images/share-icon-16x16.png\" alt=\"share_this_icon\" /> in each posting. Users can click on the link to send the contents of the post to any of a large number of social networking and bookmarking sites such as Facebook and Delicious, or by email. If you wish, you can have icons for eg Twitter, Facebook, email etc as well. Further information at <a href=\"http://sharethis.com\">Share This</a>. This version requires PodHawk 1.71 or later.";
	$this->author = "Peter Carter";
	$this->contact = "cpetercarter@googlemail.com";

	$this->initialParams = array(
		"publisher_code"  => "",
		"button_position" => "below_post", 
		"icons"           => "sharethis", 
		'iconopts'        => 'standard', 
		'text_label'      => "st_text");

	$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
	$this->enabled = (!empty($data['enabled'])) ? $data['enabled'] : 0;

	$this->listeners = array('onPostingDataReady', 'addHeadScript');

	$this->available_icons =array(
		'sharethis'     => array('Share This', 'Share'),
		'twitter'       => array('Twitter', 'tweet'),
		'facebook'      => array('Facebook', 'share'),
		'delicious'     => array('Delicious', 'share'),
		'digg'          => array('Digg', 'Digg'),
		'google_bmarks' => array('Google Bookmarks', 'bookmark'),
		'yahoo_bmarks'  => array('Yahoo Bookmarks', 'bookmark'),
		'myspace'  		=> array('MySpace', 'share'),
		'email'         => array('E-mail', 'E-mail')
		);

	$this->icon_options = array('standard', 'large', 'button', 'hcount', 'vcount');

	$this->text_options = array ('notext', 'st_text', 'all_text');

	}

protected function backendPluginsPage()  {

	$link = '{$posting.share_this_button}';

	$icons = explode(',', $this->params['icons']);
	foreach ($this->available_icons as $name => $available_icon)
	 {
		$checked[$name] = (in_array($name, $icons)) ? ' checked="checked"' : '';
	}
		
	foreach ($this->icon_options as $option) 
	{
		$checked[$option] = ($this->params['iconopts'] == $option) ? " checked=\"checked\"" : '';
	}

	foreach ($this->text_options as $option) 
	{
		$checked[$option] = ($this->params['text_label'] == $option) ? " checked=\"checked\"" : '';
	}

	$html = <<<EOF

	<tr>
		<td class="left">Your Share This Publisher Code</td>
		<td class="center"><input type="text" name="publisher_code" value="{$this->params['publisher_code']}" /></td>
		<td class="right">Got to <a href="http://sharethis.com" target="_blank">Share This</a> and register your site. Share This will give you a publisher code - you can find it at the bottom of your My Account page. It will look a bit like this - <code>7329eace-8b97-4330-8cfe-dc13fg466249</code>. Note - no quotation marks.</td>
	</tr>
	<tr>
		<td class="left">Where do you want your Share This button to appear?</td>
		<td class="center">
EOF;
$link_position_options = array("below_post" => "Below the posting text", "free" => "Somewhere else within the postings loop");

$html .= $this->makeOptions($link_position_options, "button_position"); 
	
$html .= <<<EOF
		</td>
		<td class="right">If you choose "Below the Posting Text", the plugin will automatically place the button below each posting. If you want to display the button somewhere else inside the posting loop (eg immediately after the title), select "Somewhere else within the postings loop" and add the Smarty tag <code>$link</code> in the index.tpl file for your theme, in the place where you want the link to appear.</td>
	</tr>
	<tr>
		<td class="left">Which icons do you want to display?</td>
		<td class="center">
EOF;
	
	foreach ($this->available_icons as $name => $value) {

		$html .= "<input type=\"checkbox\" name=\"icons[]\" value=\"$name\"{$checked[$name]} /> {$value[0]}<br />";
		
		}

$html .=<<<EOF
		<br />
		</td>
		<td class="right">The Share This icon allows users to share to any of the supported social and bookmarking services. But you can add other icons if you wish.</td>
	</tr>
	<tr>
		<td class="left">More icon choices</td>
		<td class="center">
			<input type="radio" name="iconopts" value="standard"{$checked['standard']} /> Standard icons<br />
			<input type="radio" name="iconopts" value="large"{$checked['large']} /> Large icons<br />
			<input type="radio" name="iconopts" value="button"{$checked['button']} /> Buttons<br />
			<input type="radio" name="iconopts" value="hcount"{$checked['hcount']} /> Icons with horizontal counter<br />
			<input type="radio" name="iconopts" value="vcount"{$checked['vcount']} /> Icons with vertical counter<br /><br />
		</td>
		<td class="right"></td>
	</tr>
	<tr>
		<td class="left">Do you want a text label beside your icons?<br />
		(Standard icons only)</td>
		<td class="center">
			<input type="radio" name="text_label" value="notext"{$checked['notext']} /> No labels<br />
			<input type="radio" name="text_label" value="st_text"{$checked['st_text']} /> Label beside 'Share This' icon only<br />
			<input type="radio" name="text_label" value="all_text"{$checked['all_text']} /> Label all icons<br /><br />
		</td>
		<td class="right">Lables are added automatically if you select the "buttons" option or one of the counter options for your icons. Large icons do not display labels.</td>
	</tr>		 
EOF;

	return $html;

	}

protected function getParamsFromPosts() {

	$params['publisher_code'] = (isset($_POST['publisher_code'])) ? $_POST['publisher_code'] : "";
	$params['button_position'] = (isset($_POST['button_position'])) ? $_POST['button_position'] : "below_post";
	$params['icons'] = (isset($_POST['icons'])) ? implode($_POST['icons'], ',') : 'sharethis';
	$params['iconopts'] = (isset($_POST['iconopts'])) ? $_POST['iconopts'] : 'standard';
	$params['text_label'] = (isset($_POST['text_label'])) ? $_POST['text_label'] : 'st_text';

	return $params;

	}

public function onPostingDataReady($postings) {

	if (ACTION == "webpage" && !empty($this->params['publisher_code']))
	{

		$changed = array();
		$text = '';	
		$required_icons = explode(',', $this->params['icons']);

		foreach ($postings as $key=>$posting)
		{

			$span = '';

			foreach ($required_icons as $icon)
			{
				$text = '';
				$class = 'st_' . $icon;

				if ($this->params['iconopts'] != 'standard')
				
				{
					$class .= '_' . $this->params['iconopts'];
				}

				if ($this->params['text_label'] == 'st_text' && $icon == 'sharethis') 

				{
					$text = ' displayText="Share this"';
				}
		
				elseif ($this->params['text_label'] == 'all_text' OR 
						$this->params['iconopts'] == 'button')

				{
					$text_to_display = $this->available_icons[$icon][1];
					$text = ' displayText="' . $text_to_display . '"';
				}
			
				$p = new PO_Permalink($key);
				$url = $p->permalink();
				$title = $posting['title'];	
				$span .= "<span class=\"$class\" $text st_url=\"$url\" st_title=\"$title\"></span>";

			}
	

			if ($this->params['button_position'] == "below_post")
			{

				$this->postingFooter[$key] = $span;

			} 
			else
			{
	
				$changed[] = array(
						"plugin"   => $this->myName,
						"variable" => "postings",
						"offset"   => array($key, "share_this_button"),
						"value"    => $span);

			}
			
		}

	return $changed;
			
	}
}

public function addHeadScript()  {

	$return = array();
	$code = '';

	if (ACTION == 'webpage' && !empty($this->params['publisher_code']))
	{
		$code = "<script type=\"text/javascript\" src=\"http://w.sharethis.com/button/buttons.js\"></script>
		<script type=\"text/javascript\">
			stLight.options({
				publisher:'" . $this->params['publisher_code'] . "',
				headerTitle:'" . SITENAME . "',
				onhover: false
				});
		</script>";
		

		$return[] = $code;
	
	}

	return $return;
	}
		
}
?>
