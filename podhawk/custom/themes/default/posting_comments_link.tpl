{*   default theme - link to comments   *}
 
        	{if $posting.comment_on == true}
                <a href="{$rss_feed}?id={$key}&amp;com=1"><img src="{$path_to_template}/images/feedicon.gif" alt="Feed icon" /></a>
            	<a href="index.php?id={$key}#{if $settings.acceptcomments == 'disqus'}disqus_thread{else}comments{/if}">{$trans.comments} ({$posting_comments_count.$key})</a> | 
            	{/if}
