{*  puts subscribe links into sidebar with option of including feeds for individual categories  *}

<ul>

<li><a href="{$rss_feed}" title="{$trans.rss_link}"><img src="{$path_to_template}/images/{#feed_icon#|default:'feed.png'}" align="top" alt="Feed" style="border:none;" /> {$trans.rss}</a></li>
<li><a href="{$rss_comment_feed}" title="{$trans.rss_comments_link}"><img src="{$path_to_template}/images/{#feed_icon#|default:'feed.png'}" align="top" alt="Feed" style="border:none;" /> {$trans.rss_comments}</a></li>

{if $with_categories == true}

	{foreach from=$category_list item=category}
	{if empty($category.hide)}
	<li><a href="{$settings.url}/podcast.php?cat={$category.name|html_to_url}" title="{$trans.rss_link} - {$category.name}"><img src="{$path_to_template}/images/{#feed_icon#|default:'feed.png'}" align="top" alt="Feed" style="border:none;" /> {$trans.rss} - {$category.name}</a></li>
	{/if}
	{/foreach}

{/if}

</ul>

{*
Configuration variables

feed_icon - the name of the RSS feed icon/image in the theme images folder

*}
