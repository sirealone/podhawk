{*   template for authors1 page   *}

{include file='manager_head.tpl'}

<body id="authors1">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
<p class="msg" id="message">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>

{if $admin == true}

<h2>{$trans.editauthors}</h2>


<table>
<tr><th>{$trans.nickname}</th><th>{$trans.fullname}</th><th>{$trans.mail}</th><th></th></tr>

{foreach from=$authors item=author key=id}
<tr>
<td><a href="index.php?page=authors2&amp;do=editauthor&amp;id={$author.id}">{$author.nickname}</a>{if $author.admin == true} [Admin]{/if}</td>
<td><a href="index.php?page=authors2&amp;do=editauthor&amp;id={$author.id}">{$author.realname}</a></td>
<td><a href="mailto:{$author.mail}">{$author.mail}</a></td>

<td class="right">
<form method="post" action="index.php?page=authors1&amp;do=delauthor&amp;id={$author.id}" onSubmit="return yesno('{$trans.deleteauthor}')">
<input type ="hidden" name="auth" value="{$authors1_auth_key}" />
<input type="submit" value="{$trans.delete}" /></form>
</td>
</tr>
{/foreach}

<form method="post" action="index.php?page=authors2&amp;do=newauthor" name="newAuthor" onsubmit ="return validateNewAuthorForm()">
<input type ="hidden" name="auth" value="{$authors2_auth_key}" />
     <tr>
<td><input type="text" name="newnick" id="newnick" value="" /></td>
<td><input type="text" name="newname" value="" /></td>
<td><input type="text" name="newmail" value="" /></td>
<td class="right">
<input type="submit" value="{$trans.new}" />

</td></tr></form></table>

{/if}

</div> <!-- close content  -->

{include file='manager_footer.tpl'}


