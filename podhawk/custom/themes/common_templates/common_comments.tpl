{*  common comments template  *}

{if $settings.acceptcomments == 'disqus'}

{if empty($smarty.get.preview)}
<div id="disqus_thread"></div>
<script type="text/javascript">
  
	var disqus_identifier='post{$key}';
	var disqus_title = "{$posting.title_uncoded}";
	var disqus_url = '{$posting.permalink}'; 
    
	(function() {ldelim}
	var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
	dsq.src = 'http://{$settings.disqus_name}.disqus.com/embed.js';
	(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	{rdelim})();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript=listentoenglish">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>
{/if}

{else}

<h3{if $comment_preview == false && empty($comment_warning)} id="commentheadline"{/if}>{$trans.comments}</h3>	

{*   We loop the comments   *}

	{if count($posting_comments.$this_post) > 0}
	<ol class="commentlist">
	{foreach from=$posting_comments.$this_post item=comment name=comments_loop}

		<li {if $comment_preview == true && $comment.id == 0 && empty($comment_warning)}id="commentheadline"{else}id="com{$comment.id}"{/if}{if $smarty.foreach.comments_loop.iteration & 1} class="alt"{/if}><div class="comment_author">{$comment.name} {$trans.says}:</div> 
		<p class="metadate">{$comment.posted|date_format:$settings.preferred_date_format}</p>
		
	{if $comment.audio_type != 0}
		<object type="application/x-shockwave-flash"
		data="podhawk/custom/themes/common_templates/emff_comments.swf?src={$comment.downloadlink}"
		width="200" height="62">
		<param name="movie" value="podhawk/custom/themes/common_templates/emff_comments.swf?src={$comment.downloadlink}" />
		{if $smarty.foreach.comments_loop.iteration & 1}
		<param name="bgcolor" value="{#comments_player_bg_1#}" />
		{else}
		<param name="bgcolor" value="{#comments_player_bg_2#}" />
		{/if}
		</object>
		<p><a href="{$comment.downloadlink}">{$trans.download_comment}</a> ({$comment.audio_size|getmegabytes} MB | {$comment.audio_length|getminutes} mins)</p>
	{/if}
		{$comment.message_html}
		</li>
		
	{/foreach}
	</ol>
	{else}
	<p>{$trans.no_comments}</p>
	{/if}

<!--  form for submitting comments   -->
{if $posting.comment_on == 1} {* if commenting is active on this posting *}

<div id="commentform">
<h3 class="reply"{if !empty($comment_warning)} id="commentheadline"{/if}>{$trans.your_comment}</h3>

{if !empty($comment_warning)}<p class="comment_warning">{$trans.$comment_warning}</p>{/if}

<form method="post" action="index.php?id={$this_post}#commentheadline" enctype="multipart/form-data">

<table>
<tr>
	<td class="table_left"><p>{$trans.name}</p></td>
	<td class="table_right"><input type="text" name="commentname" id="commentname" value="{$comment_data.name}" tabindex="1" /></td>
</tr>

<tr>
	<td class="table_left"><p>{$trans.email}</p></td>
	<td class="table_right"><input type="text" name="commentmail" id="commentmail" value="{$comment_data.mail}" tabindex="2" /></td>
</tr>	

<tr>
	<td class="table_left"><p>{$trans.your_url}</p></td>
	<td class="table_right"><input type="text" name="commentweb" id="commentweb" value="{$comment_data.web}" tabindex="3" /></td>
</tr>

{if $settings.acceptcomments == 'loudblog'}
<tr>
	<td class="table_left"><p>{$trans.spam_message} : "{$settings.spamquestion}"</p></td>
	<td class="table_right"><input type="text" name="commentspam" id="commentspam" value="{$comment_data.nospam}" tabindex="3" /></td>
</tr>
{/if}

<tr>
	<td class="table_left"><p>{$trans.your_message}</p></td>
	<td class="table_right"><textarea name="commentmessage" id="commentmessage" class="commentmessage" rows="10" cols="60">{$comment_data.message}</textarea></td>	
</tr>

<tr>
{if $posting.comment_size > 0}
	<td class="table_left"><p>{$trans.upload_audio_message} {$posting.comment_size|getmegabytes} MB)</p></td>
	<td class="table_right"><input type="file" name="commentfile" accept="audio/*" id="commentfile" value="" /></td>
{else}
	<td><p>{$trans.no_audio}</p></td>
{/if}
</tr>

<tr>
	<td class="table_left"><input type="submit" name="commentpreview" id="commentpreview" value="{$trans.preview}" /></td>
	<td class="table_right">
{if $sendbutton_test == true}
	<input type="submit" name="commentsubmit" id="commentsubmit" value="{$trans.send}" />
{/if}
	</td>
</tr>
</table>

{if $temp_audio_file_uploaded == true}
<input type="hidden"  "name="filethrough" value="{$temp_audio_file}" />
{/if}
</form>
</div>
{elseif $posting.comment_on == 2} {* if commenting is closed *}
<p>{$trans.comments_closed}</p>
{/if} 

{/if}  {*  close 'not disqus' condition  *}
