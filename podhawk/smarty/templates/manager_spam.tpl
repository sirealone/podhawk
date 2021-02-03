 {*   template for spam page   *}

{include file='manager_head.tpl'}

<body id="spam">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	<h3>{$trans.subhead}</h3>
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>{/if}


<form action="index.php?page=spam&amp;do=clearall" method="post">
<input type="hidden" name="auth" value="{$spam_auth_key}" />
<input class="clearall" type="submit" value="{$trans.clear_all}" />
</form>

<table>
<tr><th>{$trans.date}</th><th>{$trans.author}</th><th>{$trans.comment}</th><th>{$trans.posting}</th><th></th></tr>

{foreach from=$spam item=item}
<tr onmouseover="$(this).css('background-color', 'white');" onmouseout="$(this).css('background-color', {if $warning == true}'#FFEFEF'{else}'#F4F4E3'){/if};">
<td>{$item.posted|date_format}</td><td>{$item.author|truncate:15:'...'}</td>
<td>{$item.message_html|truncate:80:'...'}</td><td>{$item.posting_title}</td>
<td>
{if $item.may_delete == true}
<form action="index.php?page=spam&amp;do=notspam&amp;id={$item.id}" method="post">
<input type="hidden" name="auth" value="{$spam_auth_key}" />
<input type="submit" value="{$trans.not_spam}" />
</form>
{/if}
</td>
</tr>
{/foreach}
</table>


</div><!--  close content  -->
{include file='manager_footer.tpl'}
