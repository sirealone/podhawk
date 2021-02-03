{*   template for authors2 page   *}

{include file='manager_head.tpl'}

<body id="authors2">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>

{if $admin == true}

<h2>{$trans.authordetails} {$author.nickname}</h2>

<form action="index.php?page=authors2&amp;do=saveauthor&amp;id={$author.id}" method="post">
<input type="hidden" name="auth" value="{$authors2_auth_key}" />
<table>
<tr><td class="left">{$trans.joined}:</td><td>{$author.joined|strtotime|date_format:'%d-%b-%Y'}</td></tr>
<tr><td class="left">{$trans.nickname}:</td><td><input type="text" name="at_nickname" value="{$author.nickname}" /></td></tr>
<tr><td class="left">{$trans.login_name}:</td><td><input type="text" name="at_login_name" value="{$author.login_name}" /></td></tr>
<tr><td class="left">{$trans.fullname}:</td><td><input type="text" name="realname" value="{$author.realname}" /></td></tr>
<tr><td class="left">{$trans.mail}:</td><td><input type="text" name="mail" value="{$author.mail}" /></td></tr>

<tr><td class="left">{$trans.rightshort1}:</td><td class="explain"><input name="edit_own" type="checkbox" {if $author.edit_own == true}checked="checked"{/if} />{$trans.right1}</td></tr>
<tr><td class="left">{$trans.rightshort2}:</td><td class="explain"><input name="publish_own" type="checkbox" {if $author.publish_own == true}checked="checked"{/if} />{$trans.right2}</td></tr>
<tr><td class="left">{$trans.rightshort3}:</td><td class="explain"><input name="edit_all" type="checkbox" {if $author.edit_all == true}checked="checked"{/if} />{$trans.right3}</td></tr>
<tr><td class="left">{$trans.rightshort4}:</td><td class="explain"><input name="publish_all" type="checkbox" {if $author.publish_all == true}checked="checked"{/if} />{$trans.right4}</td></tr>
<tr><td class="left">{$trans.rightshort5}:</td><td class="explain"><input name="admin" type="checkbox" {if $author.admin == true}checked="checked"{/if} />{$trans.right5}</td></tr>
<tr><td class="left">{$trans.hide}</td><td class="explain"><input name="hide" type="checkbox" {if $author.hide == true}checked="checked"{/if} />{$trans.hide2}</td></tr>

<tr><td class="left">{$trans.changepass1}:</td><td><input type="password" name="new_password" value={if $no_password == true}""{else}"default"{/if} /></td></tr>
<tr><td class="left">{$trans.changepass2}:</td><td><input type="password" name="new_password2" value={if $no_password == true}""{else}"default"{/if} /></td></tr>

<tr><td class="left"></td><td>
<input type="submit" name="update" value="{$trans.save}" /></td></tr>
</table>
</form>
<br /><br />
<p><a href="index.php?page=authors1">{$trans.back}</a></p>

{/if}
</div> <!-- close content -->

{include file='manager_footer.tpl'}
