{*  monthly archive  *}

{monthly_archive}
	{if $monthly_archive|@count > 0}
		<ul>			
		{foreach from=$monthly_archive item=month}
			{if !isset($since) || (isset($since) && $month >= $since)}
			<li><a href="podcast.php?date={$month|date_format:"%Y-%m"}"><img src="{$path_to_template}/images/{#feed_icon#|default:'feed.png'}" alt="RSS" border="0" align="top" /></a> <a href="index.php?date={$month|date_format:"%Y-%m"}">{$month|date_format:"%B %Y"}</a></li>
			{/if}
		{/foreach}
		</ul>	
	{/if}	

{*
Configuration variables

feed_icon - the name of the RSS feed icon/image in the theme images folder (default = "feed.png")

*}
