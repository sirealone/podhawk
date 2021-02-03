	{*   default theme - centre container   *}

	{*  note: the disqus 'recent comments' box does not display well with this theme  *}

	<div class="container">
        	<h2>{$trans.last_5_comments}</h2>
		{last_comments number=5}
		{if $recent_comments|@count > 0}
        	<ul>
			
        		{foreach from=$recent_comments item=comment name=comments_loop}			
            	<li><a href="index.php?id={$comment.posting_id}#com{$comment.id}" title="{$trans.comment_link}">{$comment.name} ({$comment.posting_title})</a>
			{if $with_message == true}
		
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
    	</div>

