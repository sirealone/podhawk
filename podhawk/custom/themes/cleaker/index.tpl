{include file='common:head.tpl'}

<body>

<div id="wrapper">
	
	{*  the page header   *}

	{include file='page_header.tpl'}

	{*   navigation bar at top of page   *}
	
	{include file="upper_nav_bar.tpl"}

<div id="content">


{*   start the postings loop  *}

{if $postingdata|@count > 0}  {*  do we have posts to show  *}

{foreach from=$postingdata key=key item=posting name=postings_loop}
	
<div class="post">

	{*  show the title, date and posting message  *}

		{include file='posting_title.tpl'}

	<div class="clearfloats">
		{include file='common:posting_body.tpl'}
	</div>

	{*    hyperlinks loop    *}

		{include file='common:posting_hyperlinks.tpl' new_tab=true}

	<div class="postmeta">

		{*    show the categories--    *}

		{include file="common:posting_categories.tpl"} 

		{*    --and the tags    *}

		{include file='common:posting_tags.tpl'}

		{*    comments link, if comments are allowed    *}

		{include file='common:posting_comments_link.tpl'}

		{*    author, date, number of downloads    *}

		{include file='posting_author.tpl'}
	
		{include file='common:posting_downloads.tpl'}
	
	  </div> {*  close class = metadata   *}

	  <br />

	{if isset($posting.audio_file) && $posting.audio_file != ""}

	{*    the flash or video player    *}

	{include file='common:flashplayer.tpl'}
	
	{*    the download link    *}
		
	{include file='common:posting_download_link.tpl'}

	{/if}

	{*  the list of comments and form for new comments - only shown if a single post is requested   *}

	{if $accept_comments == true}
		
		<div id="comments">
		
		{include file='comments.tpl' this_post=$key}

		</div>
		
	{/if}

	</div>  {*  close class = post  *}

{/foreach}    {*    end the postings loop    *}

{else}  {*  if we have no posts  *}

	{include file='common:no_posts.tpl'}

{/if}


{*  the navigation links at the bottom of the page   *}

	{include file="lower_nav_links.tpl"}

</div>  {*  close id = content   *}


	{*  now the sidebars   *}

	{include file='sidebar_right.tpl'}

	{include file='sidebar_left.tpl'}

	{*  and the footer   *}
    
	{include file="footer.tpl'}
 
</div>  {*  close wrapper  *}


{*   the bit to enable disqus to find the number of comments on each post   *}

	{include file="common:disqus_footer.tpl"}

{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}


</body>

</html>
