	{*  navigation block for sidebar  *}

<ul>
<li><a href="index.php" title="{$trans.home_page}">{$trans.home_return}</a></li>
{if $nextpage == true}<li><a href="{$next_page_url}" title="{$trans.next_page}">{$trans.earlier}</a></li>{/if}
{if $previouspage == true}<li><a href="{$previous_page_url}" title="{$trans.previous_page}">{$trans.later}</a></li>{/if}

{if $with_feeds == true}
	<li><a href="{$rss_feed}"><img src="{$path_to_template}/images/{#feed_icon#|default:'feed.png'}" alt="RSS" border="0" align="top" /> {$trans.rss}</a></li>
	<li><a href="{$rss_comment_feed}"><img src="{$path_to_template}/images/{#feed_icon#|default:'feed.png'}" alt="RSS" border="0" align="top" /> {$trans.rss_comments}</a></li>
{/if}

{*  add further links to the list here if you wish  *}

</ul>

{*
Configuration variables

feed_icon - the name of the RSS feed icon/image in the theme images folder

*}
