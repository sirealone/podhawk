<?php

class ping extends PluginPattern
{
	private $delicious_enabled;
	private $twitter_status;
	private $log;
	
	function __construct($data=null)
	{

		$this->myName = "ping";
		$this->myFullName = "Ping";
		$this->version = "1.1";
		$this->description = "When you put a new post on air, this plugin allows you:<ul><li> - to ping major feed reading services</li><li> - to send an email<li> - to send a tweet to your Twitter stream.</li><li> - to bookmark your new post on Delicious.</li><li> - if you use the PodHawk Facebook facility, to send a notification to people who have authorised your Facebook application.</li></ul>This version of Ping runs on Podhawk 1.8 and later.";
		$this->author = "Peter Carter";
		$this->contact = "cpetercarter@googlemail.com";
		$this->initialParams = array(	"weblogs" 				=> 0,
										"pingomatic" 			=> 0,
										"facebook" 				=> 0,
										"email"					=> 0,
										"twitter"				=> 0,
										"twitterKey" 			=> "",
										"twitterSecret" 		=> "",
										"twitterAccessToken" 	=> "",
										"tweetText" 			=> "New posting ||title|| on ||sitename||. See it at ||url||?id=||id||",
										"delicious"				=> 0,
										'deliciousUserName'		=> '',
										'deliciousPassword'		=> '',
										'deliciousTime'			=> 0,
										'emailRecipients' 		=> '',
										'emailText' 			=> '' );

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;

		$this->enabled = $data['enabled'];

		$this->settings = $this->getSettings();

		$this->listeners = array("onPingPage", "onPing", "onBackendPluginsPage");
$this->debug = true;

		$this->twitter_status = $this->getServiceStatus('twitter');

		$this->delicious_enabled = ($this->enabled
									&&  ($this->params['delicious'] == 1)
									&& !empty($this->params['deliciousUserName'])
									&& !empty($this->params['deliciousPassword']));

		$this->log = LO_ErrorLog::instance();	
	}


	protected function backendPluginsPage()
	{

		$twitter_status = $this->getServiceStatus('twitter');
		$delicious_status = $this->getServiceStatus('delicious');

		$html = <<<EOF
		<script type="text/javascript">
		$(document).ready(function(){
		showRowsFromCheckbox('email');
		showRowsFromCheckbox('delicious');
		showRowsFromCheckbox('twitter');			
		});
		</script>
		<tr>
			<td class="left">Ping Weblogs</td>
			<td class="center">
EOF;
		$html .= $this->makeCheckBox (1, "weblogs");
		$html .=<<<EOF
			</td>
			<td class="right">
			</td>
		</tr>
		<tr>
			<td class="left">Ping Pingomatic</td>
			<td class="center">
EOF;
		$html .= $this->makeCheckBox (1, "pingomatic");
		$html .=<<<EOF
			</td>
			<td class="right">
			Pingomatic will ping many other ping/weblog services.
			</td>
		</tr>
EOF;

		$html .= <<<EOF
			<hr />
			<table id="email_table">
			<tr>
				<td class="left"><b>Send an email</b></td>
				<td class="center"> 		
EOF;

			$html .= $this->makeCheckBox (1, 'email');
			$html .=<<<EOF
				</td>
				<td></td>
			</tr>
			<tr class="email">
				<td colspan="3" class="right">
				PodHawk can send an email when a new posting goes on air, provided that your server has a mail Transfer Agent like 'Sendmail'. Use this facility to notify a limited number of people of the new posting eg collaborators, key clients. If you need to send emails to large numbers of subscribers, use eg the FeedBurner email subscription service.
				</td>
			</tr>

			<tr class="email">
				<td class="left">Whom do you want to send emails to?</td>
				<td class="center">
				<textarea name="emailRecipients" rows="4">{$this->params['emailRecipients']}</textarea>
				</td>
				<td class="right">
				A comma-separated list of email addresses eg <code>bill.gates@microsoft.com, obama@whitehouse.com</code>.
				</td>
			</tr>
			<tr class="email">
				<td class="left">The text of your email</td>
				<td class="center">
				<textarea name="emailText" rows="4">{$this->params['emailText']}</textarea>
				</td>
				<td class="right">You can use ||url|| to insert your site url, ||sitename|| to insert your site name, ||id|| to insert the id of the post you are putting on air, and ||title|| to insert the post title.</td>
			</tr>
			</table>
	
			<hr />
EOF;

	if (function_exists('curl_init'))
	{
	$html .=<<<EOF
			<table id="twitter_table">
			<tr>
				<td class="left"><b>Send a Tweet to my Twitter stream</b></td>
				<td class="center">
EOF;
			$html .= $this->makeCheckBox (1, "twitter");
			$html .= <<<EOF
				</td>
				<td class="right"></td>
			</tr>
			<tr class="twitter">
				<td colspan="3" class="right">
				You need to obtain authorisation from Twitter to send tweets from your PodHawk site. The first step is to register your website as a Twitter application at <a href="http://twitter.com/apps">twitter.com/apps</a>. Twitter will give you a Consumer Key and a Consumer Secret. Enter them in the boxes below. For further help, see the <a href="http://sourceforge.net/apps/mediawiki/podhawk/index.php?title=Twitter">PodHawk Wiki.</a>
			</td>
			</tr>
			<tr class="twitter">
				<td class="left">Twitter Consumer Key</td>
				<td class="center"><input type="text" name="twitterKey" value="{$this->params['twitterKey']}" /></td>
				<td class="right">You can reset your Consumer Key ...</td>
			</tr>
			<tr class="twitter">
				<td class="left">Twitter Consumer Secret</td>
				<td class="center"><input type="text" name="twitterSecret" value="{$this->params['twitterSecret']}" /></td>
				<td class="right">...and your Consumer Secret at <a href="http://twitter.com/apps">twitter.com/apps</a></td>
			</tr>
			<tr class="twitter">
				<td class="left">Text of the tweet to send when you put a new post on air.</td>
				<td class="center"><textarea name="tweetText" rows="4">{$this->params['tweetText']}</textarea></td>
				<td class="right">Maximum 140 characters! You can use ||url|| to insert your site url, ||title|| to insert the post title and ||permalink|| to insert the URL of your posting.</td>
			</tr>
EOF;

			if ($this->twitter_status == 0)
			{
				$html .= <<<EOF
			<tr class="twitter">
				<td class="left">Twitter Status</td>
				<td class="center">Not enabled</td>
				<td class="right">
				To use the Twitter feature:
				<ul>
				<li>enter the Consumer Key and the Consumer Secret above</li>
				<li>check the "Send a Tweet.." box</li>
				<li>enable the Ping application</li>
				<li>click 'Save'</li></ul>
				</td>
			</tr>
EOF;
			}
			elseif ($this->twitter_status == 1)
			{	
				$authorize_url = $this->getTwitterRequestToken();
				$url = THIS_URL;
				$html .= <<<EOF
			<tr class="twitter">
				<td class="left">Twitter Status</td>
				<td class="center">You have not yet authorised PodHawk to send tweets to your Twitter stream.</td>
				<td class="right">
				<a href="$authorize_url">
				<img src="$url/podhawk/custom/plugins/{$this->myName}/sign_with_twitter.png" alt="Sign in with Twitter" title="Get Twitter authorisation for this app to send tweets to your stream" />
				</a>
				</td>				
			</tr>				
EOF;
			}
			elseif ($this->twitter_status == 2)
			{
				$auth = $_REQUEST['auth'];
	
				if (isset($_SESSION['twitter_feedback']))
				{
					$html .= "<tr class=\"twitter\"><td colspan=\"3\" class=\"right\">" . $_SESSION['twitter_feedback'] . "</td></tr>";
					unset ($_SESSION['twitter_feedback']);
				}
				$latest_tweet = $this->getTweet();

				$html .=<<<EOF
			<tr class="twitter">
				<td class="left">Twitter status</td>
				<td class="center">
				You have authorised PodHawk to send tweets to the Twitter stream of {$this->params['twitterAccessToken']['screen_name']}.
				</td>				
				<td class="right">You can send a test tweet from the text box at the foot of this page!</td>
			</tr>
			<tr class="twitter">
				<td class="left">Your latest Tweet</td>
				<td class="center"><small>{$latest_tweet[0]}<br />Sent at : {$latest_tweet[1]}</small></td>
				<td></td>
			</tr>
EOF;

			}


			$html .=<<<EOF
			</table>
			<hr />
			<table id="delicious_table">
			<tr>
				<td class="left"><b>Bookmark with Delicious</b></td>
				<td class="center">
EOF;
			$html .= $this->makeCheckBox(1, 'delicious');
			$html .= <<<EOF
				</td>
				<td class="right"></td>
			</tr>
			
			<tr class="delicious">
				<td class="left">Your delicious user name</td>
				<td class="center"><input type="text" name="deliciousUserName" value="{$this->params['deliciousUserName']}" /></td>
				<td class="right"></td>
			</tr>
			<tr class="delicious">
				<td class="left">Your delicious password</td>
				<td class="center"><input type="text" name="deliciousPassword" value="{$this->params['deliciousPassword']}" /></td>
				<td class="right"></td>
			</tr>		
EOF;

		
	} // close 'if curl_init'
	else
	{
		$html .=<<<EOF
			<br />
			<tr>
				<td colspan="3">The PHP cURL extension is not on your server. Sorry, you need this extension to use the Twitter and Delicious features of this plugin.</td>
			</tr>
			<br />
EOF;
	}

		$html .= "	</table>
					<hr />
					<table>";	
	 return $html;		

	}

	protected function backendPluginsPage2()
	{

		if (!function_exists('curl_init'))
		{
			return '';
		}

		$html = "";		

		if ($this->twitter_status == 2)
		{
			$html .= "<br /><h4>Twitter</h4><br />";

			$auth = $_REQUEST['auth'];
	
			
			$html .= <<<EOF
		<form action="index.php?page=plugins&amp;edit={$this->myName}&amp;do=send_tweet" method="post">
		<input type="hidden" name="auth" value="$auth" />
		<table>
		<tr>
			<td class="left">Send a tweet!</td>
			<td class="center"><textarea name="tweetToSend" rows="4"></textarea></td>
			<td class="right"><input type="submit" value="Send Tweet" name="tweet_test" class="savebutton" />
		</tr>		
		</table>
		</form>

		<form action="index.php?page=plugins&amp;edit={$this->myName}&amp;do=reset_twitter_auth" method="post">
		<input type="hidden" name="auth" value="$auth" />
		<table>
		<tr>
			<td class="right" colspan="2">If you have problems sending tweets from your PodHawk installation, perhaps your Twitter authorisation token has expired. You can reset the token by clicking here.
			</td>
			<td class="right">
			<input type="submit" value="Reset Twitter Token" name="twitter_reset" class="savebutton" />
			</td>
		</tr>
		</table>
		</form>
			
EOF;
		}		

		if ($this->delicious_enabled)
		{
			$html .= '<br /><h4>Delicious</h4></br>';
			$auth = $_REQUEST['auth'];
			$bookmark = $this->getBookmark();
			if ($bookmark != false)
			{
				$xml = simplexml_load_string($bookmark);
				$atts = $xml->attributes();
				$user = (string)$atts['user'];
				$html .= '<p>Most recent bookmarks for <a href="http://www.delicious.com/' . $user . '" target ="_blank">' . $user .'</a></p><ul>';
				$i = 0;
				foreach ($xml->post as $post)
				{
					$atts = $post->attributes();
					$html .= '<li><a href="' . (string)$atts['href'] . '">' . (string)$atts['description'] . '</a>';
					if (isset($atts['extended'])) 
					{
						$html .= '<br /> <i>' . (string)$atts['extended'] . '</i>';
					}
					$posted = (string)$atts['time'];
					$posted = date('d M Y H:i', strtotime($posted));
					$html .= '<br />Posted ' . $posted . '</li>';
					$i++;
					if ($i == 5) break;
				}
				$html .= '</ul>';
			}
			else
			{
				$html .= "<p>Sorry - I have not been able to retrieve your bookmarks.</p>";
			}
		}

		return $html;

	}

	protected function getParamsFromPosts()
	{

		$params = $this->params;

		// $_POST values are returned only from checked checkboxes
		$options = array("weblogs", "pingomatic", "facebook", "twitter", 'delicious', "email");
		foreach ($options as $option)
		{
			$params[$option] = (isset($_POST[$option])) ? $_POST[$option] : 0;
		}

		$more_options = array('twitterKey', 'twitterSecret', 'tweetText', 'deliciousUserName', 'deliciousPassword', 'emailRecipients', 'emailText', 'emailSenderEmail', 'emailSenderName');

		foreach ($more_options as $option)
		{
			$params[$option] = (isset($_POST[$option])) ? $_POST[$option] : '';
		}
	
		return $params;

	}

	public function onPing($data)
	{

		// if a new posting id put on air, set the variable $ping = true
	 	$changed[] = array("plugin"=>$this->myName, "variable" => "ping", "value"=>true);
		return $changed;

	}

	public function onPingPage()
	{

		include (PATH_TO_ROOT . '/podhawk/lib/IXR_Library.php');
	
		$pingfeed = THIS_URL."/podcast.php";
			
		if ($this->params['weblogs'] == 1)
		{
			echo "<br /><br />Weblogs..";
			$awl_client = new IXR_Client('http://rpc.weblogs.com/RPC2');

			if (!$awl_client->query('weblogUpdates.ping', SITENAME, THIS_URL))
			{
				echo $awl_client->getErrorMessage();
			}
			else
			{
				echo "..ok!";
			}
		}

		if ($this->params['pingomatic'] == 1)
		{
			echo "<br /><br />Pingomatic ..";
			$pom_client = new IXR_Client('http://rpc.pingomatic.com/');

			if (!$pom_client->query('weblogUpdates.ping', SITENAME, THIS_URL, "", $pingfeed))
			{
				echo $pom_client->getErrorMessage();
			}
			else
			{
				echo "..ok!";
			}
		}

		if ($this->params['email'] == 1)
		{

			echo "<br /><br />Sending emails ......";

			$p = new HT_Minimal;

			$args['to'] = $this->params['emailRecipients'];
			$args['fromEmail'] = $this->params['emailSenderEmail'];
			$args['fromName'] = $this->params['emailSenderName'];
			$args['message'] = $p->purify($this->parseTweet($this->params['emailText']));

			$mailer = new MA_PingMailer();

			$sent = $mailer->send_emails($args);

			if ($sent)
			{
				echo '...success.';
			}
			else
			{
				echo '...sorry, there has been an error.<br />';
	
				$errors = $mailer->getDebugger();
				print_r ($errors);
			}
		}		

		if ($this->getServiceStatus('twitter') == 2)
		{
			echo "<br /><br />Sending tweet to Twitter....";
	
			$tweet = $this->parseTweet($this->params['tweetText']);

			$feedback = $this->sendTweet($tweet);

			echo $feedback;
		}

		if ($this->delicious_enabled)
		{
			echo '<br /><br />Bookmarking with delicious...';
				
			$result = $this->makeBookmark();

			if ($result) 
			{
				$xml = simplexml_load_string($result);

				$atts = $xml->attributes();

				if ($atts['code'] == 'done')
				{				
					echo '..success.';
				}
				else
				{
					echo $atts['code'];
				}
			}
			else
			{
				echo '...sorry, there has been an error.';
			}
		}
		
	}

	public function onBackendPluginsPage()
	{

		if (isset($_GET['do']) && isset($_GET['edit']) && $_GET['edit'] == $this->myName)
		{
			if ($_GET['do'] == "twitteraccess")
			{		
				$this->getTwitterAccessToken();
				unset ($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			}

			if ($_GET['do'] == "send_tweet")
			{
				$this->sendTweet($_POST['tweetToSend']);
			}
		
			if ($_GET['do'] == "reset_twitter_auth")
			{
				$this->changeParam("twitterAccessToken", "");
			}
		}
	}

	private function getServiceStatus ($service)
	{

		// status 0 = checkbox not ticked or application key or secret boxes empty, or app not enabled;
		// status 1 = checkbox checked and application key and secret boxes completed
		// status 2 = checkbox checked, application key and secret boxes completed, access token in database

		$status = 0;
		$key = $service . 'Key';
		$secret = $service . 'Secret';
		$token = $service . 'AccessToken';
		if ($this->enabled && $this->params[$service] == 1 && !empty($this->params[$key]) && !empty($this->params[$secret]))
		{
			if (empty($this->params[$token]))
			{
				$status = 1;
			}
			else
			{
				$status = 2;
			}
		}

	return $status;
	}

	private function getTwitterRequestToken()
	{

		require_once PLUGINS_DIR . $this->myName . "/TwitterAPI.php";
		$auth = $_REQUEST['auth'];

		$callbackURL = THIS_URL . "/podhawk/index.php?page=plugins&amp;edit=" . $this->myName ."&amp;do=twitteraccess&amp;auth=". $auth;
		$connection = new TwitterAPI('twitter', $this->params['twitterKey'], $this->params['twitterSecret']);
		$request_token = $connection->getRequestToken($callbackURL);
		$_SESSION['oauth_token']  = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		$authorize_url = $connection->getLoginURL($request_token);
		return $authorize_url;

	}

	private function getTwitterAccessToken()
	{

		if (isset($_SESSION['oauth_token']) && isset($_REQUEST['oauth_token']) && ($_SESSION['oauth_token'] == $_REQUEST['oauth_token']))
		{
			require_once PLUGINS_DIR . $this->myName . "/TwitterAPI.php";
			$obj = new TwitterApi('twitter', $this->params['twitterKey'], $this->params['twitterSecret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$access_token = $obj->getAccessToken($_REQUEST['oauth_verifier']);

			$this->changeParam("twitterAccessToken", $access_token);
			
			return true;
		}

		else return false;
	}

	private function parseTweet($text)
	{

		$posting = new PO_Posting_Extended($_GET['id']);

		$title = my_html_entity_decode($posting->getCol('title'));

		$permalink = $posting->getPermalink();		

		$search = array (	"||title||",
							"||sitename||",
							"||url||",
							"||id||",
							'||permalink||');

		$replace = array(	"\"" . $title . "\"",
							$this->settings['sitename'],
							THIS_URL,
							$_GET['id'],
							$permalink);

		$return = str_replace($search, $replace, $text);
		return $return;
	}

	private function sendTweet ($tweet)
	{

		if ($this->getServiceStatus('twitter') == 2)
		{
			$token = $this->params['twitterAccessToken']['oauth_token'];
			$secret = $this->params['twitterAccessToken']['oauth_token_secret'];

			require_once PLUGINS_DIR . $this->myName . "/TwitterAPI.php";

			$obj = new TwitterApi('twitter', $this->params['twitterKey'], $this->params['twitterSecret'], $token, $secret);

			$content = $obj->doPost('https://api.twitter.com/1/statuses/update.json', array('status' => $tweet));
			$result = $obj->return_code;
			$content = json_decode($content);

			if ($result == "200") $feedback = "Success : tweet sent";
			else $feedback = "Sorry, there is a problem - Twitter returned the code " . $result . " : " . $content->error;
		}
		else
		{
			$feedback = "You cannot send a tweet until you have authorised Twitter to accept tweets from your PodHawk application.";	
		}
	
		$_SESSION['twitter_feedback'] = $feedback;

		return $feedback;
	}

	private function getTweet()
	{
		require_once PLUGINS_DIR . $this->myName . "/TwitterAPI.php";

		$token = $this->params['twitterAccessToken']['oauth_token'];
		$secret = $this->params['twitterAccessToken']['oauth_token_secret'];

		$obj = new TwitterApi('twitter', $this->params['twitterKey'], $this->params['twitterSecret'], $token, $secret);
		if ($obj)
		{
			$content = $obj->doGet('https://api.twitter.com/1/users/show.json', array('screen_name' => $this->params['twitterAccessToken']['screen_name']));
			$content = json_decode($content);
			$date = (isset($content->status)) ? substr($content->status->created_at, 0, 16) : "";
			$text = (isset($content->status)) ? $content->status->text : "";
		
			return array($text, $date);
		}
		else
		{
			return false;
		}
	}

	private function getBookmark()
	{

		return $this->sendDeliciousRequest ('https://api.del.icio.us/v1/posts/recent');

	}

	private function makeBookmark()
	{

		// get basic info about the posting
		$posting = array();

		if (PH_CACHING == true) // use cached version of $posting if available
		{
			$cache = new DA_Cache('posting' . $_GET['id']);

			$cachedPosting = $cache->getFromCache();

			if ($cachedPosting)
			{
				$posting = $cachedPosting;
			}
		}
		
		if (empty($posting)) // otherwise get it from database
		{
			$posting = PO_Posting::findPosting($_GET['id']);

			$posting['permalink'] = PO_Posting_Extended::getPermalink($_GET['id']);
		}

		$url = rawurlencode($posting['permalink']);

		$description = rawurlencode(my_html_entity_decode($posting['title']));

		// delicious now wants comma separated tags, so replace spaces with commas, and underscores with spaces (in that order!)
		$out = array(' ', '_');
		$in = array(',', ' ');

		$tags = rawurlencode(str_replace($out, $in, $posting['tags']));

		$extended = (isset($posting['summary'])) ? rawurlencode(my_html_entity_decode($posting['summary'])) : '';

		$replace = 'yes';

		$shared = 'yes';

		$request = "https://api.del.icio.us/v1/posts/add?url={$url}&description={$description}";

		if (!empty($tags))
		{
			$request .= "&tags={$tags}";
		}

		if (!empty($extended))
		{
			$request .= "&extended={$extended}";
		}
		
		$request .= "&replace={$replace}&shared={$shared}";

		$response = $this->sendDeliciousRequest($request);

		return $response;		
	}

	private function sendDeliciousRequest($request)
	{
		$pwd = $this->params['deliciousUserName'] . ':' . $this->params['deliciousPassword'];
		
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $request);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_USERPWD, $pwd);

		curl_setopt($curl, CURLOPT_USERAGENT, 'PodHawk experimental application');

		$response = curl_exec($curl);

		$http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($http != 200)
		{
			$this->log->write("Error in connection to $request. Returned http code was $http.");
			curl_close($curl);
			return false;
		}

		curl_close($curl);

		return $response;
	}
} //close class
?>
