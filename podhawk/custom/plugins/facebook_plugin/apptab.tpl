<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">

<head>
<title>{$settings.sitename} - {$settings.slogan}</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
<link type="text/css" rel="stylesheet" href="{$settings.url}/podhawk/custom/plugins/facebook_plugin/apptab.css" />

{foreach from=$og_tags item=tag}
{$tag}
{/foreach}

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

{if $jw_player_js_embed == true}
	<script type="text/javascript" src="{$settings.url}/podhawk/custom/players/jwplayer/jwplayer.js"></script>
{/if}

{if $mp3_player_required == true}	
    <script type="text/javascript" src="{$settings.url}/podhawk/custom/players/onepixelout/audio-player-uncompressed.js"></script>  
    <script type="text/javascript">  
          AudioPlayer.setup("{$settings.url}/podhawk/custom/players/onepixelout/player.swf", {ldelim}width: 290, initialvolume: 100, transparentpagebg: "yes", bg: "FFFFFF",leftbg: "627AAD",lefticon: "3B5988",voltrack: "777B81", volslider: "3B5988", rightbg: "627AAD", rightbghover: "2A4280", righticon: "3B5988", righticonhover: "777B81",loader: "4B6781",track: "6D86B7",tracker: "3B5988",border: "666666",skip: "666666",text: "000000"{rdelim});  
    </script>
{/if}

{foreach from=$postingdata item=posting name=headloop}

	{if $posting.playertype == 'jwvideo' && $jw_player_js_embed == false}
	<script type="text/javascript">
	var flashvars = 
		{ldelim}
		{foreach from=$posting.flashvars key=key item=value name=flashvars_loop}'{$key}': '{$value}'{if !$smarty.foreach.flashvars_loop.last},{/if}{/foreach}			
		{rdelim}
	var params =
		{ldelim}
		'allowfullscreen':        'true',
        'allowscriptaccess':      'always',
        'bgcolor':                '#FFFFFF'
		{rdelim}
	var attributes =
		{ldelim}
			
		{rdelim}
	swfobject.embedSWF('{$settings.url}/podhawk/custom/players/jwplayer/player.swf', 'player_{$smarty.foreach.headloop.iteration}', '{$posting.video_player_width}', '{$posting.video_player_height}', '9.0.124', false, flashvars, params, attributes);
	</script>
	{/if}

{/foreach}
</head>

<body>

<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {ldelim}
    FB.init({ldelim}appId: '{$app_id}',
				status: true,	
				cookie: true,
             	xfbml: true
			{rdelim});
	FB.Canvas.setAutoGrow();
	FB.Canvas.scrollTo(0,0);
	  {rdelim};
  (function() {ldelim}
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/{$locale}/all.js';
    document.getElementById('fb-root').appendChild(e);
  {rdelim}());
	
</script>

<h1><a href="{$settings.url}">{$settings.sitename}</a> - <small>{$settings.description}</small></h1>
<br />

<fb:bookmark></fb:bookmark>

<p>{if !empty($previouspage)}<a href="{$previouspageurl}" title="Previous page - {$previouspageurl}">Previous Page</a>{$space}{/if}
{if !empty($nextpage)}<a href="{$nextpageurl}" title="Next page - {$nextpageurl}">Next Page</a>{$space}{/if}
{if !empty($base_url)}<a href="{$base_url}">Home</a>{$space}{/if}
<a href="{$settings.url}" target="_blank" title="{$settings.sitename} - opens in new window">Website</a>{$space}
<img src="{$settings.url}/podhawk/custom/plugins/facebook_plugin/feed.png" alt="rss" class="rss_image" />
<a href="{$feed}" target="_blank" title="RSS Feed - opens in new window">RSS Feed</a></p>
<p><a href="{$page_tab_url}">Add these podcasts as a Page Tab to a Facebook Page which you administer</a></p>

<div class="post_divider"><hr /></div>
{if $postingdata|@count > 0}  {  *are there posts to show?  *}

{*   start the postings loop  *}

{foreach from=$postingdata key=key item=posting name=postings_loop}

<div class="posting">
<h2><a href="{$posting.post_url}">{$posting.title}</a></h2>
<p><small>{$posting.posted|date_format:$settings.preferred_date_format}</small></p>

{if !empty($posting.image)}

	
	<p class="image_para"><img src="{$posting.image}"  width="100px" alt="posting_image" class="posting_image" /></p>
	

{/if}

{if $single_post == true || $posting.shortened == true}

{$posting.message_html}

{else}

{$posting.message_html|html_substr:600:'...more':$posting.post_url}

{/if}

<div class="share_button">
{* <fb:share-button class="url" href="{$posting.permalink}" /> *}

<fb:like href="{$posting.permalink}" layout="standard" show-faces="true" width="450" action="like" colorscheme="light" />
</div>

{include file="page_flashplayer.tpl"}


</div>

<div class="post_divider"><hr /></div>

{/foreach}

{else}

<p>There are no posts to display</p>

{/if}


</body>
</html>
