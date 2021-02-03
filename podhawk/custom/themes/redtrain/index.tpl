{*   index template for red train   *}

	{*  the head section  *}

	{include file='common:head.tpl'}

<body>

<div id="container">

		{*  the page header  *}

		{include file='page_header.tpl'}
		
		{*   the sidebar  *}
		
		{include file='sidebar.tpl'}



<div id="content">

{if $postingdata|@count > 0}  {*  have we got any postings to show?  *}

	{foreach from=$postingdata item=posting key=key name=postings_loop} {*  open the postings loop *}

	{*  the posting title and message  *}
		
		{include file='posting_title.tpl'}
		
		<div class="clearfloats">
		{$posting.message_html}
		</div>

	{*  download link and flash player  *}

		{if isset($posting.audio_file) && $posting.audio_file != ""} {*  is there an audio or video file?  *}

		{include file='common:posting_download_link.tpl'}	
		
		<div class="flash">
		{include file='common:flashplayer.tpl'}			
		</div>

		{/if}
  
	{*  hyperlinks   *}

		<div class="posting-links">
		
		{include file='common:posting_hyperlinks.tpl'}

		</div>

	{* date, author, comments link, categories and tags  *}

		<div class="posting-footer">
		<p>

		{*  the author *}
		
		{include file='posting_author.tpl'} | 
 		
		{*  the comments link  *}

		{include file='common:posting_comments_link.tpl'}<br />

		{*  posting categories  *}

		{include file='common:posting_categories.tpl'}<br />

		{*  posting tags  *}

		{include file='common:posting_tags.tpl'}</p>

		{*  the download counter  *}
		
		{include file='common:posting_downloads.tpl'}
		
		</div> {*  close posting-footer  *}

	{*  comments section - shown only when single post is requested  *}

	{if $accept_comments == true}

		{include file='common:comments.tpl' this_post=$key}

	{/if}

{/foreach}  {*  end the postings loop  *}

{else}  {*  if we have no posts to display  *}

	{include file='common:no_posts.tpl'}

{/if}

	{*  navigation at the foot of the page  *}
	
	{include file='footer_navigation.tpl'}

	{*  credits etc at foot of page  *}
	
	{include file='page_footer.tpl'}	

</div>{*  close content  *}

</div>{*  close container  *}

	{*  some JavaScript to enable disqus to count comments  *}

	{include file='common:disqus_footer.tpl'}

	{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}
		
</body>
</html>
