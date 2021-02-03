{*   template for sqlite page   *}

{include file='manager_head.tpl'}

<body id="sqlite">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>SQLite Manager</h1>	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">

{if $open == false}

<p>{$trans.closed}</p>

<br />
<form action="index.php?page=sqlite&amp;do=toggle" method="post">
<input type="hidden" name="auth" value="{$sqlite_auth_key}" />
<input type="submit" value="Open SQLite!" />
</form>

{else}

<p>{$trans.open}</p>

<br />
<form action="index.php?page=sqlite&amp;do=toggle" method="post">
<input type="hidden" name="auth" value="{$sqlite_auth_key}" />
<input type="submit" value="Close SQLite!" />
</form>

{/if}



</div><!-- close content  -->

{include file='manager_footer.tpl'}


