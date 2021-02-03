<?php

class facebook_plugin extends PluginPattern
{

public $facebookAppEnabled;
public $appPageEnabled;
public $ogTagsEnabled;
public $socialPluginsEnabled;
private $og = array(); // array of data for building Open Graph meta tags

	function __construct($data=null)
	{
		$this->myName = 'facebook_plugin';
		$this->myFullName = 'Facebook Plugin';
		$this->version = '1.1';
		$this->description = 'The PodHawk Facebook Plugin allows you to create a Facebook application for your podcasts. You can <ul><li>create an application page for your podcasts on Facebook</li><li>add <a href="http://developers.facebook.com/docs/reference/api/">Open Graph</a> tags to your webpage to give information to Facebook about your podcasts</li><li>add "Like" buttons or other Facebook <a href="http://developers.facebook.com/docs/plugins/">social plugins</a> to your webpages.</li></ul>First you need to register an application with Facebook, and enter the details of the application in the boxes below. Details on how to do this are <a href="http://sourceforge.net/apps/mediawiki/podhawk/index.php?title=Facebook_integration">here</a>.<br /> This version of the plugin requires PodHawk 1.8 or later.';
		$this->author = 'Peter Carter';
		$this->contact = 'cpetercarter@googlemail.com';
		$this->initialParams = array(	'app_name' => '',
										'app_id' => '',
										'app_secret' => '',
										'page_id' => '',
										'app_page' => false,
										'canvas_page' => '',
										'cats' => '0',
										'locale' => 'en_GB',
										'user_locale' => false,
										'og_tags' => false,
										'fb_id' => '',
										'app_namespace' => '',
										'social_plugins' => false,
										'like_button' => '0',
										'like_position' => 'below',
										'like_layout' => 'standard',
										'like_font' => 'verdana',
										'like_color' => 'light',
										'like_width' => 255									
										);

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;

		$this->enabled = $data['enabled'];

		$this->reg = Registry::instance();

		$this->facebookAppEnabled 	= $this->enabled &&
									!empty($this->params['app_name']) &&
									!empty($this->params['app_id']) &&
									!empty($this->params['app_secret']);

		$this->appPageEnabled 		= $this->facebookAppEnabled &&
									$this->params['app_page'] &&
									!empty($this->params['canvas_page']);

		$this->ogTagsEnabled		= $this->facebookAppEnabled &&
									$this->params['og_tags'] &&
									!empty($this->params['fb_id']);

		$this->socialPluginsEnabled = $this->ogTagsEnabled &&
									$this->params['social_plugins'];

		$this->listeners = array('onRedirect', 'onPostingDataReady', 'addMetatags', 'addNamespace', 'addBodyScript', 'onCreateMenu');

	}

	protected function backendPluginsPage()
	{
		$categories = $this->getCategories();
		$cat_options['0'] = "All categories";
		foreach ($categories as $category)
		{
			$cat_options[$category['id']] = $category['name'];
		}

		$like_button_position = array(	'below' 	=> 'below each post',
										'elsewhere' => 'Elsewhere in your post');

		$like_button_layout = array('standard' 			=> 'Standard (no photos)',
									'standard_faces' 	=> 'Standard with profile photos',
									'button_count' 		=> 'Button count',
									'box_count' 		=> 'Box count');

		$like_button_font = array(	'arial' 	=> 'arial',
									'lucida' 	=> 'lucida grande',
									'segoe' 	=> 'segoe ui',
									'tahoma' 	=> 'tahoma',
									'trebuchet' => 'trebuchet ms',
									'verdana' 	=> 'verdana');

		$like_button_colorscheme = array('light' => 'light', 'dark' => 'dark');

		$insert = '{$posting.fb_like_button}';

		$html = '';

		if (!function_exists('curl_init'))
		{
			$html .= "<p><b>Sorry, the PHP cURL Extension is not installed on your server. The Facebook plugin needs this extension to communicate with Facebook's servers. Without it, only some of the features of the plugin will work. Talk to your web host or server administrator about installing the cURL extension.</b></p>";
		}
			
		$html .= <<<EOF
		<script type="text/javascript">
		$(document).ready(function(){
		showRowsFromCheckbox('app_page');
		showRowsFromCheckbox('og_tags');
		showRowsFromCheckbox('social_plugins');
		showRowsFromTwoCheckboxes('like_button', 'social_plugins');					
		});
		</script>
		
		<tr>
			<td class="left">The name of your Facebook application</td>
			<td class="center">
				<input name="app_name" type="text" value="{$this->params['app_name']}" />
			</td>
			<td class="right">The name which you gave to your Facebook application when you registered it.</td>
		</tr>

		<tr>
			<td class="left">Application ID</td>
			<td class="center">
				<input name="app_id" type="text" value="{$this->params['app_id']}" />
			</td>
			<td class="right">The Application ID which Facebook has given to your application.</td>
		</tr>

		<tr>
			<td class="left">Your Facebook secret key</td>
			<td class="center">
				<input name="app_secret" type="text" value="{$this->params['app_secret']}" />
			</td>
			<td class="right">The secret key which Facebook gave you when you registered your application</td>
		</tr>

		<tr>
			<td class="left">The ID of your "fan page" on Facebook (optional)</td>
			<td class="center">
				<input name="page_id" type="text" value="{$this->params['page_id']}" />
			</td>
			<td class="right">The numerical ID or the name of the Facebook "<a href="http://www.facebook.com/pages/learn.php">fan page</a>" for your podcasts, if you have one.</td>
		</tr>

		<tr>
			<td class="left">Do you want an application page on Facebook?</td>
			<td class="center">
EOF;
		$html .= $this->makeCheckbox (1, 'app_page');
		$html .= <<<EOF
			</td>
			<td class="right">PodHawk can create an "application page" on Facebook. The "application page" is similar to your normal webpage, but with Facebook styling.</td>
		</tr>
		
		<tr class="app_page" style="background-color: white;">
			<td class="left">The address of your application's canvas page</td>
			<td class="center">
				<input name="canvas_page" type="text" value="{$this->params['canvas_page']}" />
			</td>
			<td class="right">	The address of your application's canvas page (WITH trailing slash eg http://apps.facebook.com/myapp/)</td>
		</tr>

		<tr class="app_page" style="background-color: white;">
			<td class="left">Which category of postings do you want on your Facebook page?</td>
			<td class="center">
EOF;

		$html .= $this->makeOptions ($cat_options, 'cats');

		$html .= <<<EOF
			</td>
			<td class="right" style="background-color: white;">Your Facebook page can show postings from all categories, or from just one category.</td>
		</tr>

		<tr>
			<td class="left">Add Open Graph tags to your web pages?</td>
			<td class="center">			
EOF;

			$html .= $this->makeCheckbox (1, 'og_tags');

			$html .= <<<EOF
			</td>
			<td class="right">If you want the FaceBook "like" button or other social plugins on your web pages, you must enable Open Graph tags and fill in the required details.</td>
		</tr>

		<tr class="og_tags bgwhite">
			<td class="left">Your Facebook user id OR your Facebook user name.</td>
			<td class=center">
				<input type="text" name="fb_id" value="{$this->params['fb_id']}" />
			</td>
			<td class="right">Guidance on how to find your Facebook user id or user-name is <a href="">here</a></td>
		</tr>

		<tr class="og_tags bgwhite">
			<td class="left">The "namespace" of your application</td>
			<td class="center">
				<input type="text" name="app_namespace" value="{$this->params['app_namespace']}" />
			</td>
			<td class="right">The "namespace" which you registered for your Facebook application.</td>
		</tr>

		<tr class="og_tags bgwhite">
			<td class="left">Your locale</td>
			<td class="center">
				<input type="text" name="locale" value="{$this->params['locale']}" />
			</td>
			<td class="right">By default, PodHawk creates Facebook elements (eg "like" buttons) in British English. If you want a different language, choose one of the language codes ("locales") in <a href="http://fbdevwiki.com/wiki/Locales">this list</a>.</td>
		</tr>

		<tr class="og_tags bgwhite">
			<td class="left">Or use user's own language?</td>
			<td class="center">
EOF;

		$html .= $this->makeRadioButtons('user_locale');

		$html .= <<<EOF
			</td>
			<td class="right">Alternatively, PodHawk can try to find the Facebook user's own "locale" and use that instead.</td>
		</tr>.

		<tr>
			<td class="left">Use Facebook social plugins on your web pages?</td>
			<td class="center">
EOF;

		$html .= $this->makeCheckbox (1, 'social_plugins');

		$html .= <<<EOF
			</td>
			<td class="right">Check this box to add the Facebook Javascript SDK to your webpage. You can then use the option below to add a like button to your posts. You can add other social plugins by hand simply by adding the appropriate &lt;fb:...&gt; tags to your theme template. Make sure that you also enable Open Graph tags.</td>
		</tr>

		<tr class="social_plugins">
			<td class="left">Add Facebook "like" button to your posts?</td>
			<td class="center">
EOF;
	
		$html .= $this->makeCheckbox(1, 'like_button');

		$html .= <<<EOF
		
			</td>
			<td class="right"></td>
		</tr>
		
		<tr class="like_button bgwhite">
			<td class="left">Where should the "like" button be placed?</td>
			<td class="center">
EOF;

		$html .= $this->makeOptions ($like_button_position, 'like_position');

		$html .= <<<EOF
			</td>
			<td class="right">If you choose 'below each post', PodHawk will automatically place the "like" button at the end of the post. If you choose 'elsewhere', you can place a Smarty tag $insert where you want in your theme template, inside the postings loop.</td>
		</tr>

		<tr class="like_button bgwhite">
			<td class="left">The layout of your button.</td>
			<td class="center">
EOF;
		$html .= $this->makeOptions($like_button_layout, 'like_layout');

		$html .= <<<EOF
			</td>
			<td class="right">You can find details of this and other "like" button options <a href="http://developers.facebook.com/docs/reference/plugins/like/">here</a> in the section headed "Attributes"</td>
		</tr>

		<tr class="like_button" style="background-color: white;">
			<td class="left">Font for your button.</td>
			<td class="center">
EOF;

		$html .= $this->makeOptions($like_button_font, 'like_font');

		$html .= <<<EOF
			</td>
			<td class="right"></td>
		</tr>
		
		<tr class="like_button bgwhite">
			<td class="left">Colour scheme for your button.</td>
			<td class="center">
EOF;
		$html .= $this->makeOptions($like_button_colorscheme, 'like_color');

		$html .= <<<EOF
			</td>
			<td class="right"></td>
		</tr>

		<tr class="like_button bgwhite">
			<td class="left">Width for your "like" button (in pixels)</td>
			<td class="center">
				<input type="text" name="like_width" value="{$this->params['like_width']}" />
			</td>
			<td class="right">Minimum width is 255 for standard layout, 90 for button count, 55 for box count.</td>
		</tr>
		
EOF;

if ($this->facebookAppEnabled)
{
	$thispage = urlencode(THIS_URL . "/podhawk/index.php?page=plugins");

	$html .= <<<EOF
		<tr>
			<td colspan="2"><a href="http://www.facebook.com/dialog/pagetab?app_id={$this->params['app_id']}&amp;redirect_uri=$thispage">Click here</a> to add your app as a Page Tab to any Facebook pages which you administer</td>
			
			<td class="right">You first need to enable "Page tab" in the <a href="https://developers.facebook.com/apps">Facebook Developer App</a> for your application.</td>
		</tr>
EOF;
}
			

		return $html;
	}

	protected function getParamsFromPosts()
	{
		$params = $this->params;

		$checkboxes = array('app_page','og_tags', 'user_locale', 'social_plugins', 'like_button');
		foreach ($checkboxes as $checkbox)
		{
			$params[$checkbox] = (isset($_POST[$checkbox])) ? $_POST[$checkbox] : 0;
		}

		$list = array('app_name', 'app_id', 'app_secret', 'page_id', 'canvas_page', 'cats', 'locale', 'fb_id', 'app_namespace', 'like_position', 'like_layout', 'like_font', 'like_color', 'like_width' );

		foreach ($list as $item)
		{
			if (isset($_POST[$item]))
			{
				$params[$item] = $_POST[$item];
			}
		}

		return $params;
	}

	public function onRedirect($requested)
	{
		if (strtolower($requested) == 'facebook')
		{
			$url = $this->getFanPageAddress();

			if ($url)
			{
				header ('Location: ' . $url);
				exit;
			}
			elseif ($this->facebookAppEnabled == true && !empty($this->params['canvas_page']))		
			{
				header ('Location: ' . $this->params['canvas_page']);
				exit;
			}
		}
	}

	public function onCreateMenu ()
	{
		$changed = array();

		$url = $this->getFanPageAddress();
	
		if	($url)
		{
			$changed[] = array(	'plugin' 	=> $this->myName,
								'variable' 	=> 'menu_array',
								'offset' 	=> array('Facebook', 'Your Fan Page'),
								'value' 	=> $url);
		}
		if ($this->appPageEnabled == true)
		{
			$changed[] = array( 'plugin' 	=> $this->myName,
								'variable' 	=> 'menu_array',
								'offset' 	=> array('Facebook', 'Your Application'),
								'value' 	=> $this->params['canvas_page']);
		}
		return $changed;
	}		
	
	public function onPostingDataReady($postings)
	{

		$changed = array();

		if ($this->ogTagsEnabled)
		{
			$this->makeOgTags($postings);
		}

		
		foreach ($postings as $key => $posting)
		{
			$this->postingFooter[$key] = '';

			if ($this->socialPluginsEnabled && $this->params['like_button'])
			{
				$button = $this->makeLikeButton ($key);

				if ($this->params['like_position'] == 'below')
				{
					$this->postingFooter[$key] .= $button;
				}
				else
				{
					$changed[] = array(
						"plugin"   => $this->myName,
						"variable" => "postings",
						"offset"   => array($key, "fb_like_button"),
						"value"    => $button);
				}
			}		
		}
		return $changed;	
	}

	public function makeOgTags($postings)
	{
		$namespace = (empty($this->params['app_namespace'])) ? 'og' : $this->params['app_namespace'];
		$audio = (empty($this->params['app_namespace'])) ? 'audio' : 'podcast';

		$og = array();
			if (isset($_GET['id'])) //single posting
			{
				$posting 				= $postings[$_GET['id']];
				$og['og:title'] 		= my_html_entity_decode($posting['title']);
				$og['og:type'] 			= 'article';
				$og['og:url'] 			= $posting['permalink'];
				$i 						= $posting['image'];
				$og['og:image'] 		= ($i) ? $i : THIS_URL . '/images/itunescover.jpg';
				$og['og:description'] 	= (!empty($posting['summary'])) ? my_html_entity_decode($posting['summary']) : my_html_entity_decode($this->reg->findSetting('description'));

				if ($posting['audio_type'] == '1' || $posting['audio_type'] == '2') // data for mp3 and ogg audio files
				{
					if ($namespace == 'og')
					{
						$og['og:audio'] 		= $posting['audiourl'];
					}
					else
					{
						$og["$namespace:podcast:url"] = $posting['audiourl'];
					}

					$og["$namespace:$audio:type"] 	= $posting['mime'];

					if ($posting['filelocal'] == 1)
					{
						$id3 = new ID_ReadID3(AUDIOPATH . $posting['audio_file']);

						$audio_data = $id3->getBackendID3Data();

						$required_data = array('title', 'artist', 'album');

						foreach ($required_data as $item)
						{
							if (!empty($audio_data[$item]))
							{
								$og["$namespace:$audio:$item"] = $audio_data[$item];
							}
						}
					}
				}

				else

				{
					$supportedFormats = array(7, 8, 10, 11, 16, 17, 20); // these appear to be the formats supported by Facebook

					if (in_array($posting['audio_type'], $supportedFormats))
					{				
						$videotypedata = DataTables::AudioTypeData($posting['audio_type']);

						if (file_exists(JW_DIR . 'jwplayer.swf'))
						{
							$og['og:video'] = THIS_URL . '/podhawk/custom/players/jwplayer/jwplayer.swf?file=' . rawurlencode($posting['audiourl']);
						}
						else
						{
							$og['og:video'] = $posting['audiourl'];
						}
						$og['og:video:type'] = $videotypedata['mime'];
					}
				}
		
			}
			else // multiple postings
			{
				$og['og:title'] 		= my_html_entity_decode(SITENAME);
				$og['og:type'] 			= 'blog';
				$og['og:url'] 			= THIS_URL . '/index.php';
				if ($this->params['cats'] > 0)
				{
					$og['og:url']		.= '?cat=' . $this->reg->getURLEncodedCategoryName($this->params['cats']);
				}
				$og['og:image'] 		= THIS_URL . '/images/itunescover.jpg';
				$og['og:description'] 	= my_html_entity_decode($this->reg->findSetting('description'));
			}
			
			// for both single and multiple posts
			$og['og:site_name'] 	= SITENAME;			
			$og['fb:admins'] 		= $this->params['fb_id'];			
			$og['fb:app_id'] 		= $this->params['app_id'];
			$og['og:locale']		= $this->params['locale'];
			//$og['fb:page_id'] 	= $this->params['page_id'];		not now used in Facebook	

			$this->og = $og;
		
	}

	protected function makeLikeButton($key)
	{
		$layout = $this->params['like_layout'];

		if ($this->params['like_layout'] == 'standard_faces')
		{
			$layout = 'standard';				
		}

		$link = PO_Posting_Extended::getPermalink($key);

		$tag = "<fb:like href=\"" . $link . "\" layout=\"" . $layout . "\" ";			
			
		if ($this->params['like_layout'] == 'standard_faces')
		{
			$tag .= "show-faces=\"true\" ";
		}
			
		$tag .= "width=\"" . $this->params['like_width'] . "\" ";
		$tag .= "action=\"like\" ";
		$tag .= "font=\"" . $this->params['like_font'] . "\" ";
		$tag .= "colorscheme=\"" . $this->params['like_color'] . "\">";
		$tag .= "</fb:like>";

		return $tag;
	}

			
	public function addMetatags()
	{
		$meta = array();

		foreach ($this->og as $n => $v)
		{
			if (!empty($v)) // ignore empty content
			{
				$meta[] = "<meta property=\"" . $n . "\" content=\"" . htmlspecialchars($v) . "\" />";
			}
		}

		return $meta;
	}

	public function addNamespace()
	{
		$ns = array();
		if ($this->ogTagsEnabled)
		{
			$ns[] = "xmlns:og=\"http://ogp.me/ns#\"";
			$ns[] = "xmlns:fb=\"http://www.facebook.com/2008/fbml\"";
			if (!empty($this->params['app_namespace']))
			{
				$ns[] = "xmlns:{$this->params['app_namespace']}=\"http://ogp.me/ns/apps/{$this->params['app_namespace']}#\"";
			}
		}

		return $ns;
	}

	public function addBodyScript ()
	{
		$bs = array();
		if ($this->socialPluginsEnabled)
		{
			$bs[] = <<<EOF
<div id="fb-root"></div>
<script type="text/javascript">
window.fbAsyncInit = function()
{
	FB.init({appId: '{$this->params['app_id']}', status: true, cookie: true, xfbml: true});
};

	(function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/{$this->params['locale']}/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));
</script>
EOF;
		}

	return $bs;
	}

	private function getFanPageAddress()
	{
		$url = false;

		if (!empty($this->params['page_id']))
		{
			if (ctype_digit($this->params['page_id']))
			{
				$url = 'http://www.facebook.com/profile.php?id=' . $this->params['page_id'];
			}
			else
			{
				$url = 'http://www.facebook.com/' . $this->params['page_id'];
			}
		}

		return $url;
	}
		
}

?>
