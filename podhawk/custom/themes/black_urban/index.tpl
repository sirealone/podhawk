		{*  index template for black urban theme  *}

{include file="common:head.tpl"}

<body>

<div id="body">
	<div id="header">
		<div class="logo">

		{include file="page_header.tpl"}

		</div> {* close logo *}

		<div class="links">

		{*   You can change these links. Note - it is the title which is displayed on
			the screen, not the text between <a> and </a>. Don't change the id=".."
			bit because the JavaScript in theme.js needs it. *}

		<a id="home" href="index.php" title="Home Page">Home Page</a>
		<a id="online" href="index.php" title="Link 2">Link 2</a>
		<a id="archive" href="index.php" title="Link 3">Link 3</a>
		<a id="rss" href="{$rss_feed}" title="RSS feed">RSS feed</a>

		</div>  {* close links *}

	</div>  {* close header  *}

<div id="wrapper">

<div id="container">

<div id="posts">

{if $postingdata|@count > 0}  {  *are there posts to show?  *}

{*   start the postings loop  *}

{foreach from=$postingdata key=key item=posting name=postings_loop}

	<div class="post" id="post-{$key}">

		{include file="posting_title.tpl"}

		<div class="post-entry">
			<div class="clearfloats">
			{$posting.message_html}
			</div>
			{include file='common:posting_hyperlinks.tpl'}

			{if isset($posting.audio_file) && $posting.audio_file != ""}

				<div id="flash">

				<br />

				{*    the flash or video player    *}

				{include file='common:flashplayer.tpl'}
	
				{*    the download link    *}
		
				{include file='common:posting_download_link.tpl'}

				{* 'this file has been downloaded ... times'  *}

				{include file='common:posting_downloads.tpl' mp3_only=false}

				</div>  {*  flash  *}

			{/if}

		</div> {*  close post-entry  *}

		<div class="post-footer">

			<div class="post-footer-links right">
	
			<br />

			{include file="common:posting_categories.tpl"} | 
			{include file="common:posting_tags.tpl"} | 
			{include file="common:posting_comments_link.tpl"}

			</div> {*  close post-footer-links  *}

		{*  the 'barbed wire' divider between posts *}

		<div class="post-footer-line clear"></div>

		</div>  {*  close post-footer  *}
		
		{*  the list of comments and form for new comments - only shown if a single post is requested   *}

		{if $accept_comments == true}
		
			<div id="comments">
		
			{include file='common:comments.tpl' this_post=$key}

			</div>
		
		{/if}

	</div> {*  close post  *}

{/foreach} {*  close the postings loop  *}
	
	{* navigation links below the postings *}										

	{include file="lower_nav_links.tpl"}

{else}  {*  if there are no posts to show  *}
	
	{include file='common:no_posts.tpl'}

{/if}

</div> {* close posts *}

</div> {*  close container *}

</div> {*  close wrapper *}

<div id="sidebar">

	{include file="sidebar.tpl"}

</div> {*  close sidebar  *}

<div id="footer">

	{* the category etc lists at the foot of the page  *}

 		{include file="footer_lists.tpl"}

	<p class="clear copy">Powered by PodHawk | <a href="http://anton.shevchuk.name/web20/creative-design-in-15-minutes/">Black Urban Theme</a> by <a href="http://anton.shevchuk.name" title="Anton Shevchuk">Anton Shevchuk</a></p>
 
</div>  {* close footer  *}

{*  some JavaScript to enable disqus to count comments properly  *}
	{include file='common:disqus_footer.tpl'}

{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}
		
</div> {* close id="body" *}

</body>

</html>

