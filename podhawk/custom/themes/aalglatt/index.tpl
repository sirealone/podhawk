	{*  index tpl for aalglatt theme  *}

{include file='common:head.tpl'}

<body>

<div id="container">

<hr />

{*   page header   *}

{include file='page_header.tpl'}

<div id="content_bg">
	<!-- Needed for dropshadows -->
	<div class="container_left">
	<div class="container_right">
	<div class="topline">
	<!-- Start float clearing -->
	<div class="clearfix">

{*  the sidebar   *}

	{include file='sidebar.tpl'}

<hr />

<div id="content">

{if !empty($postingdata)}  {  *are there posts to show?  *}

{*   start the postings loop  *}

{foreach from=$postingdata key=key item=posting name=postings_loop}

	{include file='posting_title.tpl'}

	<div class="main">

	<div class= "clearfloats">
		{$posting.message_html}
	</div>

	{include file='common:posting_hyperlinks.tpl'}

	{if isset($posting.audio_file) && $posting.audio_file != ""}

		{*    the flash or video player    *}

		<div class="flashplayer">
		{include file='common:flashplayer.tpl'}
		</div>
	
		{*    the download link    *}
		
		{include file='common:posting_download_link.tpl'}

	{/if}

	<div class="meta">

		{include file='common:posting_categories.tpl'}

		{include file='common:posting_tags.tpl'}

		{include file='common:posting_comments_link.tpl'}

		{include file='common:posting_downloads.tpl' mp3_only=true}

	</div>

	{*  the list of comments and form for new comments - only shown if a single post is requested   *}

	{if $accept_comments == true}
		
		<div id="comments">
		
		{include file='common:comments.tpl' this_post=$key}

		</div>
		
	{/if}


	</div>{*main*}

{/foreach} {*  close the postings loop  *}

{else}  {*  if there are no posts to show  *}
	
	{include file='common:no_posts.tpl'}

{/if}

{*  the navigation links at the bottom of the page   *}

{*	{include file="lower_nav_links.tpl"}  *}
{include file='common:pagination_string.tpl'}

</div>{*content*}

<hr />

<div id="footer">
	<p>Aalglatt Template by <a href="http://www.felixkrusch.com">Felix Krusch</a></p>


<div class="extras">
	<ul>
		<li><a href="{$rss_feed}">{$trans.rss}</a></li>
		<li><a href="{$rss_comment_feed}">{$trans.rss_comments}</a></li>
	</ul>
</div>{*extras*}

</div> {*footer*}

</div>{*clearfix*}
</div>{*topline*}
</div>{*container_right*}
</div>{*container_left*}
</div>{*content_bg*}

{*  some JavaScript to enable disqus to count comments properly  *}
{include file='common:disqus_footer.tpl'}

{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}

</div>  {*container*}
</body>

</html>
