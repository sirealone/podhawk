{* template for backend utilities page  *}

{include file='manager_head.tpl'}

<body id="utilities">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">

{if !empty($message)}
<p class="msg">{$trans.$message|default:$message}</p><br />
{/if}

{if $admin == true} {* only administrators can view this page *}

{if $xml_ok == true}
	<h2>{$trans.updates}</h2>
	<p>{$trans.version1} {$ph_version}. {$trans.version2}</p>
	<form action="index.php?page=utilities&amp;do=check_updates" method="post">
	<input type="hidden" name="auth" value="{$utilities_auth_key}" />
	<input type="submit" value="{$trans.version3}" class="wide" />
	</form>
{/if}

<h2>{$trans.database}</h2>
<p>{$trans.database1}{$trans.$db_type}. {$trans.database2}{$trans.$db_access}</p>

{if $windows == false} {* we can't use unix system commands or change permissions on a Windows machine *}
	{if $db_type == 'mysql' && $system_function_disabled == false}
		<p>{$trans.mysqlbackup}</p>
		<form action="index.php?page=utilities&amp;do=backup_database" method="post">
		<input type="hidden" name="auth" value="{$utilities_auth_key}" />
		<input type="submit" value="{$trans.backup}" class="wide" />
		</form>
	{/if}

	{if ($db_type == 'sqlite' || $db_type == 'sqlite3') && $sqlite_open == false && $sapi == "apache"}
		<p>{$trans.sqlite1}</p>
		<form action="index.php?page=utilities&amp;do=open_sqlite" method="post">
		<input type="hidden" name="auth" value="{$utilities_auth_key}" />
		<input type="submit" value="{$trans.sqlite2}" class="wide" />
		</form>
	{/if}

	{if ($db_type == 'sqlite' || $db_type == 'sqlite3') && $sqlite_open == true && $sapi == "apache"}
		<p>{$trans.sqlite_3}</p>
		<form action="index.php?page=utilities&amp;do=close_sqlite" method="post">
		<input type="hidden" name="auth" value="{$utilities_auth_key}" />
		<input type="submit" value="{$trans.sqlite4}" class="wide" />
		</form>
	{/if}

	{if $db_type == 'sqlite' || $db_type == 'sqlite3'}
		<p>{$trans.sqlite5}</p>
		<form action="index.php?page=utilities&amp;do=backup_sqlite" method="post">
		<input type="hidden" name="auth" value="{$utilities_auth_key}" />
		<input type="submit" value="{$trans.backup}" class="wide" />
		</form>
	{/if}
{/if} {* close if !$windows *}

<h2>{$trans.caches}</h2>
<p>{$trans.caches1}</p>

<form action="index.php?page=utilities&amp;do=clear_caches" method="post">
<input type="hidden" name="auth" value="{$utilities_auth_key}" />
<input type="submit" value="{$trans.caches2}" class="wide" />
</form>

{if $sapi == 'apache' && $windows == false}
	{if $cache_state == '0755'}
		<p>{$trans.caches3}</p>
		<form action="index.php?page=utilities&amp;do=open_cache" method="post">
		<input type="hidden" name="auth" value="{$utilities_auth_key}" />
		<button type="submit" class="wide">{$trans.caches4}</button>
		</form>
	{elseif $cache_state == '0777'}
		<p>{$trans.caches5}</p>
		<form action="index.php?page=utilities&amp;do=close_cache" method="post">
		<input type="hidden" name="auth" value="{$utilities_auth_key}" />
		<button type="submit" class="wide">{$trans.caches6}</button>
		</form>
	{/if}
{/if}

<h2>{$trans.logs}</h2>
	<p>{$trans.logs1}</p>
	<textarea style="width:650px; height:100px;">
	{foreach from=$error_log item=line}
		{$line}
	{/foreach}
	</textarea>
	<p>{$trans.logs2}</p>
	<textarea style="width:650px; height:100px;">
	{foreach from=$events_log item=line}
		{$line}
	{/foreach}
	</textarea>
	<p>{$trans.logs3}</p>

<h2>{$trans.cookies}</h2>
	<p>{$trans.cookies1}</p>
	<form action="index.php?page=utilities&amp;do=delete_cookies" method="post">
	<input type="hidden" name="auth" value="{$utilities_auth_key}" />
	<input type="submit" value="{$trans.cookies2}" class="wide" />
	</form>

{/if}  {*   close 'admin only' condition  *}
</div><!--   close content   -->

{include file='manager_footer.tpl'}
