{*  places list of recent comments on page - first if disqus is used, second if not   *}

{if $settings.acceptcomments == 'disqus'}

{*  NB : there is a problem with the display of Disqus comments in the sidebar of the Kubrick template. The comments move down to below the bottom of the main part of the page. This happens because Disqus adds the css property 'clear : both' to the comment items. This is a Disqus problem, not a PodHawk one, and you will need to ask Disqus how to overcome it. Sorry!  *}

<div id="dsq-recentcomments" class="dsq-widget">
<h2 class="dsq-widget-title">{$trans.recent_comments}</h2>
<script type="text/javascript" src="http://disqus.com/forums/{$settings.disqus_name}/recent_comments_widget.js?num_items=5&amp;avatar_size=32"></script>
</div>
<a href="http://disqus.com">{$trans.disqus_powered}</a>

{else}

{if #with_recent_comments_heading#|default:true}<{#comments_style#|default:h2}>{$trans.recent_comments}</{#comments_style#|default:h2}>{/if}
{last_comments number=$number}
	{if $recent_comments|@count > 0}
	<ul>

	{foreach from=$recent_comments item=comment name=recent_comments_loop}	
	<li><a href="{$comment.posting_link}#com{$comment.id}" title="{$trans.comment_link}">{#comments_image#}{$comment.name} ({$comment.posting_title})</a>
	{if $with_message == true}
		<br />
		{if isset($truncate)}
	 		<small>{$comment.message_html|strip_tags:false|truncate:$truncate}</small>
		{else}
			<small>{$comment.message_html|strip_tags:false|truncate}</small>
		{/if}
	{/if}	
	</li>
	{/foreach}
	</ul>
	{/if}
{/if}

{*
Configuration variables

with_recent_comments_heading - this template, unusually for sidebar templates, includes a heading 'Recent Comments'. Do we want to display this heading? Defaults to 'true', to maintain backwards compatibility.

comments_style - the html tag for the 'Recent Comments' heading, default is <h2....</h2>

*}

