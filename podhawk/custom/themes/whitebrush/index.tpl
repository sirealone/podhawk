{include file='common:head.tpl'}

<body>

<div id="base">

	
	{include file='page_header.tpl'}
	
		
<div id="main-top"></div>	
	
<div id="main">
	
<div id="content">

	{*   the postings loop   *}

	{if $postingdata|@count > 0}

	{foreach from=$postingdata item=posting key=key name=postings_loop}
	


			{*  the posting title, author, date   *}

			{include file='posting_title.tpl'}

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


					{* metadata - comments link, categories, tags, number of downloads  *}

					<p class="postmetadata">
					<small>
		
					{include file='common:posting_comments_link.tpl'}<br />

					{include file='common:posting_categories.tpl'}<br />

					{include file='common:posting_tags.tpl'}<br />	
				
					{include file='common:posting_downloads.tpl'}
			
					</small>
					</p>
					
					



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
	

</div> {*  close content  *}


<div id="main-bottom"></div>




	{*  sidebar  *}

	{include file='sidebar.tpl'}




	{*  page footer  *}

	{include file='footer.tpl'}
	
	
</div>  {*  close main  *}	




	{*  stuff to enable disqus to count comments  *}

	{include file='common:disqus_footer.tpl'}

	{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}
	
	
</div> {*  close base  *}

</body>
</html>
