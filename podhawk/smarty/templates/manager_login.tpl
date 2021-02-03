{*  template for the login page  *}

{include file='manager_head.tpl'}


<body id="login" onLoad="$('#login_name').focus();"> 


<div id="wrapper"{if $warning == true} class="warning"{/if}>
<div id="header">
<h1>{$trans.login}</h1>
<h3>{$site_url}</h3>
</div>



<div id="content">
<p class= "msg">{$add_message}</p><br />

<form id="loginform" class="plain" action="index.php?page=record1" method="post" onsubmit="return doCHAP()">
<input type="hidden" name="auth" value="{$record1_auth_key}" />
<table>
<tr><th><label for="login_name">{$trans.login_name}</label></th>
<th><label for="password">{$trans.password}</label></th><th></th></tr>
<tr><td><input id="login_name" type="text" name="login_name" /></td>
<td><input id="password" type="password" name="password" /></td>
<input type="hidden" name="challenge" id="challenge" value="" />
<td><input type="submit" name="submit" value="{$trans.login_button}" /></td>
</tr><tr><td colspan="3">
<input id="remember_me" name="remember_me" type="checkbox" value="1" checked="checked" />
<label for="remember_me">{$trans.remember_me}</label></td></tr>
</table>
</form>


</div>


{include file='manager_footer.tpl'}
