<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<{foreach from=$namespaces item=namespace}{$namespace} {/foreach}>
{config_load file=$config_file}

<head>
    <title>{$page_title}</title>

   	{foreach from=$metatags item=tag}
	{$tag}
	{/foreach}

	{foreach from=$css item=stylesheet}
	{$stylesheet}
	{/foreach}
    
    <link rel="alternate" type="application/rss+xml" title="Podcast-Feed" href="{$rss_feed}" />    
    
	{foreach from=$javascript item=src}{if !empty($src)}
	<script type="text/javascript" src="{$src}"></script>
	{/if}{/foreach}
	
	{if $pixout_required == true}
    <script type="text/javascript">  
          AudioPlayer.setup("{$settings.url}/podhawk/custom/players/onepixelout/player.swf", {ldelim}width: {$players.pix_width}, initialvolume: 100, transparentpagebg: "yes",bg: "{$players.pix_background}",leftbg: "{$players.pix_leftbackground}",lefticon: "{$players.pix_lefticon}",voltrack: "{$players.pix_voltrack}", volslider: "{$players.pix_volslider}", rightbg: "{$players.pix_rightbackground}", rightbghover: "{$players.pix_rightbackgroundhover}", righticon: "{$players.pix_righticon}", righticonhover: "{$players.pix_righticonhover}",loader: "{$players.pix_loader}",track: "{$players.pix_track}",tracker: "{$players.pix_slider}",border: "{$players.pix_border}",skip: "{$players.pix_skip}",text: "{$players.pix_text}"{rdelim});  
    </script>
	{/if}

	{include file="common:ipad.tpl"}

	{foreach from=$plugins_head_script item=script}
		{$script}
	{/foreach}
</head>
