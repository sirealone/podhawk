 {*   template for backend plugins page   *}

{include file='manager_head.tpl'}

<body id="plugins">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>Plugins</h1>	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
<p class="msg">{$trans.$message|default:$message}</p>

{if isset($smarty.get.edit)}

<div {if !empty($thisPluginData.html2)}class="unit"{/if}>

{assign var="name" value=$smarty.get.edit}

<h2>{$thisPluginData.fullName}</h2>
<p><b>{$trans.description} :</b> {$thisPluginData.description}</p>
<p><b>{$trans.version} :</b> {$thisPluginData.version}</p>
<p><b>{$trans.author} :</b> {$thisPluginData.author}</p>
<p><b>{$trans.contact} :</b> {$thisPluginData.contact}</p>
<br />
<form action="index.php?page=plugins&amp;edit={$name}&amp;do=save" method="post">
<input type="hidden" name="auth" value="{$plugins_auth_key}" />
<table>

{if !$thisPluginData.enabled}
<tr>
	<td colspan="3" class="right">{$trans.not_enabled_warning}</td>
</tr>
{/if}
<tr>
	<td class="left">{$trans.enable}</td>
	<td class="center">
	<input class="radio" name="enable" type="radio" value="1" {if $thisPluginData.enabled == '1'}checked="checked"{/if} />{$trans.enabled}&nbsp;&nbsp;
    	<input class="radio" name="enable" type="radio" value="0" {if $thisPluginData.enabled == '0'}checked="checked"{/if} />{$trans.disabled}
   	 </td>
    	<td class="right">
    
    	</td>
</tr>
</table>
<br />
<table>
<tr>
	<td class="left">{$trans.run}</td>
	<td class="center">
	<select name="run_order" class="narrow">
		<option value="1"{if $thisPluginData.run_order == 1} selected="selected"{/if}>1</option>
		<option value="2"{if $thisPluginData.run_order == 2} selected="selected"{/if}>2</option>
		<option value="3"{if $thisPluginData.run_order == 3} selected="selected"{/if}>3</option>
		<option value="4"{if $thisPluginData.run_order == 4} selected="selected"{/if}>4</option>
		<option value="5"{if $thisPluginData.run_order == 5} selected="selected"{/if}>5</option>
		<option value="6"{if $thisPluginData.run_order == 6} selected="selected"{/if}>6</option>
	</select>	
	</td>
	<td class="right">{$trans.run_help}</td>
</tr>
</table>
<br />

<table>
{$thisPluginData.html}

<tr>
	<td><a href="index.php?page=plugins">{$trans.backtolist}</a></td>
	<td></td>
	<td class="right">
	<input type="submit" value="Save" name="submit" class="wide" />
	</td>
</tr>
<tr>
	<td class="left"><a href="index.php?page=plugins&amp;uninstall={$thisPluginData.name}&amp;auth={$plugins_auth_key}" onclick="return yesno('{$trans.remove_confirm}')">{$trans.remove}</a></td><td></td><td></td>
</tr>
</table>
</form>

{* display the second part of the plugin page, if there is one  *}
{if !empty($thisPluginData.html2)}
</div> {* close div class="unit" *}
<div>
{$thisPluginData.html2}
{/if}

</div>

{else} {* if not editing the settings of an individual plugin  *}

<div class="unit">
<h3>{$trans.plugins_installed} :</h3><br />

<table>
{foreach from=$pluginsInstalled item="name"}
<tr>
	<td>{$pluginsData.$name.full_name|escape:htmlall}</td>
	<td> {if $pluginsData.$name.enabled == 1}<span class="green">{$trans.enabled}</span>{else}<span class="red">{$trans.disabled}</span>{/if}</td>
	<td><a href="index.php?page=plugins&amp;edit={$name}&amp;auth={$plugins_auth_key}">{$trans.edit_status}</a></td>
</tr>
{/foreach}
</table>
<br />
</div>

<div class="unit">
<h3>{$trans.plugins_not_installed} :</h3><br />
{foreach from=$pluginsNotInstalled item=plugin}
<p>{$plugin} - <a href="index.php?page=plugins&amp;install={$plugin}&amp;auth={$plugins_auth_key}">{$trans.install}</a></p>
{/foreach}
</div>

{/if}

</div><!--   close content   -->

{include file='manager_footer.tpl'}







