<?php

//a PodHawk plugin

//this plugin generates a simple xml file (audio/playerfeed[xxxxx].xml) containing data on the audio/video files
//associated with the postings displayed on the current page. It returns the location and name of the xml file
//which can then be referenced by players which use a playlist, such as the JW FLV mediaplayer (www.jeroenwijering.com).
//You need to pass the following values to the function:
// - $postings - data about the postings on the page (smarty variable '$postingdata')
// - $title - the title of the site (smarty variable '$settings.sitename')

// Example use in template :
//
//<script type="text/javascript">
//var so = new SWFObject('podhawk/custom/players/jwplayer/player.swf','mpl','470','470','9');
//so.addParam('allowscriptaccess','always');
//so.addParam('allowfullscreen','true');
//so.addParam('flashvars','&file={playerfeed postings=$postingdata sitename=$settings.sitename}&playlist=bottom');
//so.write('player');
//</script>

//version 1.2 - amended calculation of $enclosure, so that allow_url_fopen is not required

function smarty_function_playerfeed ($params)  {

global $settings;

$postings = $params['postings'];

$title = $settings['sitename'];
$link = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if (isset($_GET['cat']))  {
		$title .= " : ".$_GET['cat']; }
	elseif (isset($_GET['id']))  {
		$title .= " : ".$_GET['id'];  }

	
$content = "<rss version=\"2.0\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\">\n\n";
$content .= "<channel>\n";
$content .= "<title>".DataTables::html_to_xml($title)."</title>\n";
$content .= "<link>".DataTables::html_to_xml($link)."</link>\n";
foreach ($postings as $key=>$posting)  {

	if ($posting['filelocal'])  {
		if ($settings['countfla'])  {
			$enclosure = "get.php?fla=".$posting['audio_file'];
			}  else  {
			$enclosure = "audio/".$posting['audio_file'];
			}
		}  else  {
		$enclosure = $posting['audio_file'];
		}

	$content .= "<item>\n";
	$content .= "<title>" . DataTables::html_to_xml($posting['title']) . "</title>\n";
	$content .= "<itunes:author>" . DataTables::html_to_xml($posting['author_full_name']) . "</itunes:author>\n";
	$content .= "<link>".$settings['url'] . "?id=" . $key . "</link>\n";
	$content .= "<description>" . DataTables::html_to_xml(strip_tags($posting['message_html'])) . "</description>\n";
	$content .= "<enclosure url=\"".$enclosure. "\" length=\"".$posting['audio_size']."\" type=\"".$posting['mimetype']."\" />\n";
	$content .= "<itunes:duration>" . $posting['itunesduration'] . "</itunes:duration>\n";
	$content .= "</item>\n";
		}
$content .= "</channel>\n</rss>";

//as many users may be using this plugin simultaneously, we need to give each xml file a unique name
$time = time();
$rand = mt_rand(0,999);
$xml_file_name = "audio/playerfeed_".$time."_".$rand.".xml";


$fp = fopen($xml_file_name, 'w');
    fwrite($fp, $content, strlen($content) );
    fclose($fp);

//delete playerfeed files more than 60 minutes old (ie delete them when the cached template expires)
 $audiofolder = opendir('audio');
    while ($file = readdir($audiofolder)) {
        if (substr($file, 0, 10) == "playerfeed") {
	    $bits = explode("_",$file);
            if ((isset($bits[1])) && (($bits[1] + 3600) < time())) {
                unlink("audio/".$file);
            }
        }
    }
    closedir($audiofolder);
return $xml_file_name;
}

?>

