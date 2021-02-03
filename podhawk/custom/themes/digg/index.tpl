	{*  index tpl for digg theme  *}

{include file='common:head.tpl'}

<body>

<div id="container">

{*   page header   *}

	<div id="header">

	{*  the tabbed menu at the top of the page  *}
		<div id="menu">
		{include file='top_menu.tpl'}
		</div>

	{*  the page title   *}
		<div id="pagetitle">
		{include file='page_header.tpl'}
		</div>

	{*  the RSS links below the title  *}
		<div id="syndication">
		<a href="{$rss_feed}" title="{$trans.rss}" class="feed">{$trans.rss}</a> &#124; <a href="{$rss_comment_feed}" title="{$trans.rss_comments}">{$trans.rss_comments}</a>
		</div>

	{*  the searchbox on the right below the title  *}
		<div id="searchbox">
		{include file='header_search.tpl'}
		</div>

	</div>  {*  header  *}

<div class="pagewrapper">
<div class="page">

	{*  the left sidebar  *}

	{include file='sidebar_left.tpl'}


<div class="narrowcolumnwrapper">
<div class="narrowcolumn">

<div class="content">

	{*   the postings   *}

{if $postingdata|@count > 0}  {  *are there posts to show?  *}

	{*   start the postings loop  *}

	{foreach from=$postingdata key=key item=posting name=postings_loop}

	<div class="post" id="post-{$key}">

		{include file='posting_title.tpl'}

		<div class="entry">
		<div class="clearfloats">
		{$posting.message_html}
		</div>
		{include file='common:posting_hyperlinks.tpl'}

		{if isset($posting.audio_file) && $posting.audio_file != ""}

			{*    the flash or video player    *}

			{include file='common:flashplayer.tpl'}
	
			{*    the download link    *}
		
			{include file='common:posting_download_link.tpl' barcode=true}

		{/if}

		<div class="postinfo">
			
			{include file='common:posting_categories.tpl'}
			<br />
			{include file='common:posting_tags.tpl'}
			<br />
			{include file='common:posting_comments_link.tpl'}
			{include file='common:posting_downloads.tpl'}

		</div>

		</div>  {*  close entry  *}



	{*  the list of comments and form for new comments - only shown if a single post is requested   *}

	{if $accept_comments == true}
		
		<br />		
		
		{include file='common:comments.tpl' this_post=$key}
			
	{/if}

	</div>  {*  close post  *}
	

	{/foreach} {*  close the postings loop  *}

{else}  {*  if there are no posts to show  *}
	
	<div class="post">
	{include file='common:no_posts.tpl'}
	</div>

{/if}

	{include file='navigation_footer.tpl'}

</div></div></div>  {*   close content, narrowcolumn and narrowcolumnwrapper  *}


	{*  the right-hand sidebar  *}

	{include file='sidebar_right.tpl'}

</div></div></div>  {*  close page, pagewrapper, container  *}


	{*  some JavaScript to enable disqus to count comments properly  *}

	{include file='common:disqus_footer.tpl'}

{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}

</body>

</html>
