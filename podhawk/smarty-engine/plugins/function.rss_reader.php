<?php 

// A PodHawk plugin
//
//gets data from an rss- or atom-feed and assigns it to Smarty
//This is based on MagPieRSS and a nice php-script by Richard Eriksson
//
// The following parameters should be passed to the function :
// url - the url of the rss or atom feed
// number - the number of items to be read from the feed
//
// Example of use in template :
//
// {rss_reader number=2 url=http://www.listen-to-english.com/podcast_fb.php}
// <h2>{$feed_channel.title}</h2>
// <ul>
// {foreach from=$feed_items=feed_item}
// <li><a href="{$feed_item.link}">{$feed_item.title}</a></li>
// {/foreach}
// </ul>
//
// All the items of information in the feed eg channel title, date, author etc can be accessed with this function
// 'Channel' information is in $feed_channel eg $feed_channel.title
// Information on individual feed items is in $feed_items eg $feed_item.link
// $feed_items_counts contains the number of items that the function has actually managed to retrieve - useful if you
// want to suppress template elements when no feed is available.
function smarty_function_rss_reader ($params, &$smarty) {

$number = $params['number'];
$url = $params['url'];
   
require_once PATH_TO_ROOT ."/podhawk/magpierss/rss_fetch.inc"; 
    $magpie = fetch_rss($url);
    $items = array_slice($magpie->items, 0, $number);
	$channel = $magpie->channel;
	

$smarty->assign(array('feed_items' => $items,
			'feed_channel' => $channel,
			'feed_items_count' => count($items)));

}
?>
