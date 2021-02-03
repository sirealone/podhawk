{*  index file for Kubrick template   *}

{include file='common:head.tpl'}

<body>

<div id="page">
	
{*  the page header   *}

	{include file='page_header.tpl'}
	
	<hr />
<div id="content" class="narrowcolumn">

{*   the postings loop   *}

{if $postingdata|@count > 0}

{foreach from=$postingdata item=posting key=key name=postings_loop}

		<div class="post">

		{*  the posting title, author, date   *}

		{include file='posting_title.tpl'}

		<div class="entry">

		{*  the posting message   *}

		<div class="clearfloats">
		{$posting.message_html}
		</div>

		{if isset($posting.audio_file) && $posting.audio_file != ""}
				
			{*  the 'download this mp3' link  *}

			{include file='common:posting_download_link.tpl'}

			{*  the flash player  *}

			{include file='common:flashplayer.tpl'}

		{/if}
				
		{*  the hyperlinks  *}

		{include file='common:posting_hyperlinks.tpl'}

		</div>  {*  close entry  *}

		{* metadata - comments link, categories, tags, number of downloads  *}

			<p class="postmetadata"><small>
		
			{include file='common:posting_comments_link.tpl'}<br />

			{include file='common:posting_categories.tpl'}<br />

			{include file='common:posting_tags.tpl}<br />	
				
			{include file='common:posting_downloads.tpl'}
			
			</small></p>
				
		</div>  {*  close post  *}

		{*  list of comments and comment form - displayed only when single post is requested  *}

		{if $accept_comments == true}

			<div id="comments">

			{include file='common:comments.tpl' this_post=$key}

			</div>

		{/if}
		
{/foreach}  {*  close the posting loop  *}

	{else} {*  if there are no postings  *}

		{include file='common:no_posts.tpl'}

	{/if}

	{*  navigation links at foot of page  *}

	{include file='lower_nav_links.tpl'}

</div>  {*  close content  *}

	{*  sidebar  *}

	{include file='sidebar.tpl'}

	<hr />

	{*  page footer  *}

	{include file='footer.tpl'}

</div>  {*  close page  *}

	{*  stuff to enable disqus to count comments  *}

	{include file='common:disqus_footer.tpl'}

	{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}
</body>
</html>
