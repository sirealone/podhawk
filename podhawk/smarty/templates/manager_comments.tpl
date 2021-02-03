   {*   template for backend comments page   *}

{include file='manager_head.tpl'}

{assign var='comment' value=$comments_list.0}

<body id="comments">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	{if $smarty.get.subpage != 'edit'}
	<h3>{$paging_string}</h3>
	{/if}
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{$trans.$message|default:$message}</p>{/if}

{* Editing or deleting a comment*}
{if $smarty.get.subpage == 'edit'}
{assign var='comment' value=$comments_list.0}

<h3>{$trans.edit_comment} {$smarty.get.edit_id}, {$trans.posted} {$comment.posted|strtotime|date_format:$date_format}</h3>
<table>
<form method="post" action="index.php?page=comments&amp;do=save&amp;edit_id={$smarty.get.edit_id}{if isset($smarty.post.page_revert)}&amp;nr={$smarty.post.page_revert}{/if}{if isset($smarty.post.sort_revert)}&amp;sort={$smarty.post.sort_revert}{/if}{if isset($smarty.post.posting_id_revert)}&amp;posting_id={$smarty.post.posting_id_revert}{/if}">
<input type="hidden" name="auth" value="{$comments_auth_key}" /> 
<tr>
	<td class="left">{$trans.name}:</td>
	<td class="center">
		<input type="text" name="commentname" value="{$comment.name}" />
	</td>
	<td class="right"></td>
</tr>
<tr>
	<td class="left">{$trans.email}:</td>
	<td class="center">
{if !empty($comment.mail)}
		<p>{$comment.mail}</p>
		<p><a href="mailto:{$comment.mail}">{$trans.send_email}</a></p>
{else}
		<p>{$trans.no_email}</p>
{/if}
	</td>
	<td class="right"></td>
</tr>
<tr>
	<td class="left">{$trans.website}:</td>
	<td class="center">
{if !empty($comment.web) && $comment.web != 'http://'}
		<p><a href="{$comment.web}">{$comment.web}</a></p>
{else}
		<p>{$trans.no_website}</p>
{/if}
	</td>
	<td class="right"></td>
</tr>
<tr>
	<td class="left">{$trans.ip}:</td>
	<td class="center">{$comment.ip}</td>
	<td class="right"></td>
</tr>
<tr>
	<td class="left">{$trans.message}:</td>
	<td colspan="2">
		<textarea name="commentmessage" class="commentmessage" id="commentmessage" rows="10">{$comment.message_html}</textarea>
	</td>
</tr>
{if $comment.has_audio == true}
<tr>
	<td class="left">{$trans.has_audio}</td>
	<td class="center">
		{include file=manager_comments_flashplayer.tpl}
	</td>
	<td class="right"></td>
</tr>
{/if}

<tr>
	<td class="left">{$trans.save_changes}...</td>
	<td class="center">
			<input type="submit" value="{$trans.save}" class="wide" />			
	</td>
</tr>
</form>

<form method="post" action="index.php?page=comments&amp;do=x&amp;edit_id={$smarty.get.edit_id}&amp;{if isset($smarty.post.page_revert)}&amp;nr={$smarty.post.page_revert}{/if}{if isset($smarty.post.sort_revert)}&amp;sort={$smarty.post.sort_revert}{/if}{if isset($smarty.post.posting_id_revert)}&amp;posting_id={$smarty.post.posting_id_revert}{/if}" onsubmit=" return yesno('Do you really want to delete this comment?')">
<input type="hidden" name="auth" value="{$comments_auth_key}" />
<tr>
	<td class="left">..{$trans.delete_comment}</td>
	<td class="center">
		<input type="submit" value="{$trans.delete}" class="wide" />
	</td>
	<td class="right"></td>
</tr>
</form>

{if $akismet == true}
<form method="post" action="index.php?page=comments&amp;do=spam&amp;edit_id={$smarty.get.edit_id}{if isset($smarty.post.page_revert)}&amp;nr={$smarty.post.page_revert}{/if}{if isset($smarty.post.sort_revert)}&amp;sort={$smarty.post.sort_revert}{/if}{if isset($smarty.post.posting_id_revert)}&amp;posting_id={$smarty.post.posting_id_revert}{/if}" onsubmit=" return yesno('Do you really want to mark this comment as spam? This action will remove the comment permanently.')">
<input type="hidden" name="auth" value="{$comments_auth_key}" />
<tr>
	<td class="left">..{$trans.delete_as_spam}</td>
	<td class="center">
		<input type="submit" value="{$trans.spam}" clas="wide" />
	</td>
	<td class="right"></td>
</tr>
</form>
{/if}

{if $comment.has_audio == true}
<form method="post" action="index.php?page=comments&amp;do=delete_audio&amp;edit_id={$smarty.get.edit_id}{if isset($smarty.post.page_revert)}&amp;nr={$smarty.post.page_revert}{/if}{if isset($smarty.post.sort_revert)}&amp;sort={$smarty.post.sort_revert}{/if}{if isset($smarty.post.posting_id_revert)}&amp;posting_id={$smarty.post.posting_id_revert}{/if}" onsubmit=" return yesno('Do you really want to delete this audio file?')">
<input type="hidden" name="auth" value="{$comments_auth_key}" />
<tr>
	<td class="left">...{$trans.delete_audio}</td>
	<td class="center">
		<input type="submit" value="{$trans.delete_audio_file}" class="wide" />
	</td>
	<td class="right"></td>
</tr>
</form>
{/if}

<tr>
	<td class="left">
	<a href="index.php?page=comments{if isset($smarty.post.page_revert)}&amp;nr={$smarty.post.page_revert}{/if}{if isset($smarty.post.sort_revert)}&amp;sort={$smarty.post.sort_revert}{/if}{if isset($smarty.post.posting_id_revert)}&amp;posting_id={$smarty.post.posting_id_revert}{/if}">{$trans.return_to_list}</a>
	</td>
	<td class="center"></td>
	<td class="right"></td>
</tr>
</table>  


{* Viewing page of comments *}
{else}

<table>
<tr>
<th{if $current_sort == 'posted'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.posted}">{$trans.date}</a></th>
<th{if $current_sort == 'name'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.name}">{$trans.name}</a></th>
<th{if $current_sort == 'message_input'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.message}">{$trans.message}</a></th>
<th{if $current_sort == 'posting_id'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.posting_id}">{$trans.belong}</a></th>
<th></th>

</tr>

{foreach from=$comments_list item=comment key=key}
<tr onmouseover="$(this).css('background-color', 'white');" onmouseout="$(this).css('background-color', {if $warning == true}'#FFEFEF'{else}'#F4F4E3'){/if};" >

<td class="comment_date">{$comment.posted|strtotime|date_format:'%d-%b-%Y'}</td>
<td class="comment_name"><p>{$comment.name}</p></td>
<td class="comment_message">{$comment.message_html}
{if $comment.has_audio == true}<p class="has_audio">{$trans.has_audio_2}</p>{/if}
</td>

<td class="postings"><a href="index.php?page=record2&amp;auth={$record2_auth_key}&amp;do=edit&amp;id={$comment.posting_id}" title="{$trans.edit}">{$comment.posting_title}</a></td>

<td class="right">
{if $comment.may_delete == true}
	<form method="post" action="index.php?page=comments&amp;subpage=edit&amp;edit_id={$comment.id}">
	<input type="hidden" name="auth" value="{$comments_auth_key}" />
	{if isset($smarty.get.nr)}
		<input type="hidden" name="page_revert" value="{$smarty.get.nr}" />
	{/if}
	{if isset($smarty.get.sort)}
		<input type="hidden" name="sort_revert" value="{$smarty.get.sort}" />
	{/if}
	{if isset($smarty.get.posting_id)}
		<input type="hidden" name="posting_id_revert" value="{$smarty.get.posting_id}" />
	{/if}
	<input type="submit" value="{$trans.edit_delete}" class="wide" />
	</form>
{/if}
</td>


</tr>
{/foreach}
</table>

{/if} {* end Viewing page of comments *}

</div> <!--   close content   -->
{include file='manager_footer.tpl'}
