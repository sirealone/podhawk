{*  index template for default   *}

{include file='common:head.tpl'}

<body>

<div id="wrapper">

	{*   the page header   *}

	{include file ='page_header.tpl'}

	<div id="headbox">

	{*  the left container - welcome and links to feeds  *}
    	
	{include file='left_container.tpl'}

	{*  the centre container - comments  *}

	{include file='centre_container.tpl'}

	{*   the right container - categories and tags   *}
	
	{include file='right_container.tpl'}	
    
    	{*  div to put the image of the box bottom on screen   *}

	<div id="boxbottom"></div>
	
	</div> {*  close id=headbox  *}

	<div id="content">

	{*  the postings loop   *}

{if $postingdata|@count > 0}  {*  do we have posts to show  *}

    	{foreach from=$postingdata item=posting key=key name=postings_loop}

    		<div class="posting">

		{*  posting title   *}
        	
		{include file='posting_title.tpl'}

		{*  posting message  *}

		<div class="box">
        		{$posting.message_html}
        	</div>

		{*  hyperlinks  *}

		{include file='common:posting_hyperlinks.tpl'}

		{*  the flash player and download link   *}
		
		{if isset($posting.audio_file) && $posting.audio_file != ""}

			<div class="audiobox">

				{include file='common:flashplayer.tpl'}  
      		
        			{include file='common:posting_download_link.tpl'}

        		</div>

        	{/if}

		{*   author, comments, categories, tags etc   *}

			<p class="author">

			{include file='posting_date_and_author.tpl'}

			{include file='posting_comments_link.tpl'}

			{include file='posting_categories.tpl'}

			<br />

			{include file='common:posting_tags.tpl'}

			</p>

			{include file='common:posting_downloads.tpl'}

		{*  the list of comments and the comments form  *}	
	
			{if $accept_comments == true}
			
			{include file='comments.tpl' this_post=$key}
			
			{/if}
	

		</div> {* close class = posting  *}

	{/foreach}  {*   close the postings loop    *}
	
{else}  {*  if there are no posts to show  *}

	{include file='common:no_posts.tpl'}

{/if}

	{*   navigation at the foot of the page   *}
	
	{include file='navigation.tpl'}

	{*  footer   *}

	{include file='footer.tpl'}

</div>  <!-- close content  -->
</div>  <!-- close wrapper  -->

	{include file='common:disqus_footer.tpl'}

{* JavaScript from plugins to be placed at end of 'body'  *}
	{foreach from=$plugins_body_script item=body_script}
	{$body_script}
	{/foreach}

</body>
</html>
