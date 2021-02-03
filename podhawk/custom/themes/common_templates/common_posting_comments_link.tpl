	{*  link to the posting comments   *}

{if $posting.comment_on == true}{if !#hash_link#}<p>{/if}{#comments_link_image#}<a href="index.php?id={$key}#{if $settings.acceptcomments == 'disqus'}disqus_thread{else}comments{/if}" title="{$trans.link_comments}"> {$trans.comments} ({$posting_comments_count.$key})</a>{if !#hash_link#}</p>{/if}{/if}

{*
Configuration file variables

hash_link - suppresses the <p>..</p> tags

comments_link_image an html image tag for any image to be displayed in front of the comments link

*}
