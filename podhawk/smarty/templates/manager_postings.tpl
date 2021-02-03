   {*  template for postings page   *}

{include file='manager_head.tpl'}

<body id="postings">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	<h3>{$paging_string}</h3>
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">

{if !empty($message)}
<p class="msg">{$trans.$message|default:$message}</p><br />
{/if}

<table>
<tr>
<th{if $current_sort == 'posted'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.posted}">{$trans.date}</a></th>
<th{if $current_sort == 'author_id'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.author}">{$trans.author}</a></th>

<th{if $current_sort == 'title'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.title}">{$trans.title}</a></th>
<th></th>
<th{if $current_sort == 'audio_length'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.audio_length}">{$trans.length}</a></th>
<th{if $current_sort == 'status'} class="pink"><img src="smarty/templates/images/arrow_{if $sort_direction == 0}up{else}down{/if}.png" alt="arrow" /{/if}><a href="index.php{add_to_url att='sort' value=$direction.status}">{$trans.status}</a></th>
<th></th>

{foreach from=$post_table item=post key=key}
<tr onmouseover="$(this).css('background-color', 'white');" onmouseout="$(this).css('background-color', {if $warning == true}'#FFEFEF'{else}'#F4F4E3'){/if};">
	<td>{$post.posted|strtotime|date_format:'%d-%b-%Y'}</td><td>{$post.author_name}</td>
	<td>
		<a href="index.php?page=record2&amp;auth={$record2_auth_key}&amp;do=edit&amp;id={$post.id}">{$post.title}</a> id = {$post.id}
  	{if $post.sticky > 0} [Sticky] {/if}
	{if $post.count_comments > 0}<a href="index.php?page=comments&amp;posting_id={$post.id}" title = "Link to comments ({$post.count_comments})"><img src="smarty/templates/images/user_comment.png" alt="comments" /></a>{/if}
	</td>
	<td>{if $post.has_audio == true}{include file='manager_postings_flashplayer.tpl'}{/if}</td>
	<td>{$post.audio_length|getminutes}</td><td>{$post.status_word}</td>
	<td>{if $post.may_edit == true}
		<form method="post" action="index.php?page=postings&amp;do=x&amp;id={$post.id}" onSubmit="return yesno('{$trans.deleteposting}')">
		<input type="hidden" name="auth" value="{$postings_auth_key}" />
        <input type="submit" value="{$trans.delete}" />
       </form>
{/if}
	</td>
</tr>
{/foreach}



</table>
</div><!--   close content   -->

{include file='manager_footer.tpl'}
