	{*  index template for zig zag theme  *}

	{*  the head section of the page  *}

	{include file='common:head.tpl'}

<body>

<div id="page">

	{*  the page header  *}

	{include file='page_header.tpl'}

<hr />

<div id="content" class="narrowcolumn">

{if $postingdata|@count > 0}  {*  have we got posts to show?  *}

{foreach from=$postingdata item=posting key=key name=postings_loop}  {*  start the postings loop  *}

	<div class="post">	
	
	{*  the title, author name and posting time  *}
	
	{include file='posting_title.tpl'}
	
	{*  the posting message  *}

		<div class="entry">

		<div class="clearfloats">
		{$posting.message_html}
		</div>

		{*  the hyperlinks  *}

		{include file='common:posting_hyperlinks.tpl'}

		{*  the flash player and the download link  *}

		{if isset($posting.audio_file) && $posting.audio_file != ""}
				
			{*  the 'download this mp3' link  *}

			{include file='common:posting_download_link.tpl'}

			{*  the flash player  *}
			
			{include file='flashplayer.tpl'}
			
		{/if}

		</div>  {*  close entry  *}
		
		{*  the comments link, the posting categories and tags and the download counter  *}

		<p class="postmetadata">
		{include file='common:posting_comments_link.tpl'}
		<br />
		{include file='common:posting_categories.tpl'}
		<br />
		{include file='common:posting_tags.tpl'}
		</p>

		<p class="postmetadata">{include file='common:posting_downloads.tpl'}</p>

	</div>  {*  close post  *}

{*  list of comments and comment form - displayed only when single post is requested  *}

		{if $accept_comments == true}

			<div id="comments">

			{include file='common:comments.tpl' this_post=$key}

			</div>

		{/if}

{/foreach}  {*  end the postings loop  *}	

{else}  {*  if we have no posts  *}

	<div class="post">
	{include file='common:no_posts.tpl'}
	</div>

{/if}



{*  the next page/previous page links at the foot of the page  *}

	{include file='lower_nav_links.tpl'}

</div> {*  close content  *}

{*  the sidebar  *}

	{include file='sidebar.tpl'}

{* credits etc at the foot of the page  *}

	{include file='page_footer.tpl'}

</div> {*  close page  *}

{*  some JavaScript to enable disqus to count comments  *}

	{include file='common:disqus_footer.tpl'}

{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}
</body>

</html>	
