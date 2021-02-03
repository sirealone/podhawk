{*   comments template for default  *}

{if $settings.acceptcomments == 'disqus'}

<div id="disqus_thread"></div>
<script type="text/javascript" src="http://disqus.com/forums/{$settings.disqus_name}/embed.js"></script>
<noscript><a href="http://{$settings.disqus_name}.disqus.com/?url=ref">{$trans.view_thread}.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">{$trans.blog_comments} {$trans.disqus_powered} <span class="logo-disqus">Disqus</span></a>

{else}

<h2{if $comment_preview == false} id="comments"{/if}>{$trans.comments}</h2>	

{*   We loop the comments   *}

	{if count($posting_comments.$this_post) > 0}
	<ol class="commentlist">
	{foreach from=$posting_comments.$this_post item=comment}
		
		<li {if $comment_preview == true && $comment.id == 0}id="comments"{else}id="com{$comment.id}"{/if}><cite>{$comment.name}</cite> {$trans.says_on} {$comment.posted|date_format:$settings.preferred_date_format} :
		
	{if $comment.audio_type != 0}
		<br />
		<object type="application/x-shockwave-flash"
		data="{$path_to_template}/emff_comments.swf?src={$comment.downloadlink}"
		width="150" height="21">
		<param name="movie" value="{$path_to_template}/emff_comments.swf?src={$comment.downloadlink}" />
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
<h2>{$trans.your_comment}</h2>
<form method="post" action="index.php?id={$this_post}#comments" enctype="multipart/form-data">
<div class="input">
	<label for="commentname">{$trans.name}</label>
	<input type="text" name="commentname" id="commentname" value="{$comment_data.name}" />
</div>
<div class="input">
	<label for="commentmail">{$trans.email_address}</label>
	<input type="text" name="commentmail" id="commentmail" value="{$comment_data.mail}" />
	<p><small>{$trans.email_message}</small></p>	
</div>
<div class="input">
	<label for="commentweb">{$trans.your_url}</label>
	<input type="text" name="commentweb" id="commentweb" value="{$comment_data.web}" />	
</div>

{if $settings.acceptcomments == 'loudblog'}
<div class="input" style="clear:both;">
	<p><small>{$trans.spam_message}</small></p>
	<label for="commentspam">{$settings.spamquestion}</label>
	<input type="text" name="commentspam" id="commentspam" value="{$comment_data.nospam}" />	
</div>
{/if}

<div id="message" class="input" style="clear:both;">
	<label for="commentmessage">{$trans.your_message}</label>
	{if $settings.allow_bb_editor == true}
	<script type="text/javascript">edToolbar('commentmessage');</script>
	{/if}
	<textarea name="commentmessage" id="commentmessage" class="commentmessage" rows="30" cols="40">{$comment_data.message}</textarea>	
</div>

{if $posting.comment_size > 0}
<div id="file" class="input">
<p>{$trans.upload_audio_message} {$posting.comment_size|getmegabytes} MB)</p>
<input type="file" name="commentfile" accept="audio/*" id="commentfile" />
</div>
{else}
  <p>{$trans.no_audio}</p>
{/if}

<div id="buttons" class="input">
	<input type="submit" name="commentpreview" id="commentpreview" value="{$trans.preview}" />
{if $sendbutton_test == true}
	<input type="submit" name="commentsubmit" id="commentsubmit" value="{$trans.send}" />
{/if}
</div>
{if $temp_audio_file_uploaded == true}
<input type="hidden"  "name="filethrough" value="{$temp_audio_file}" />
{/if}
</form>
</div>  <!--  close commentform -->
{elseif $posting.comment_on == 2} {* if commenting is closed *}
<p>Comments on this post are now closed.</p>
{/if} 

{/if}  {*   close 'not disqus' condition'  *}
