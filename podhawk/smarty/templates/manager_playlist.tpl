   {*  template for the playlist page  *}

{include file='manager_head.tpl'}

<body id="playlist">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{if isset($trans.$message)}{$trans.$message}{$filename}{else}{$message}{$filename}{/if}</p>{/if}





<div class="unit">
<table>
	<tr>
		<td colspan="3"><h3>{$trans.create_playlist}</h3></td>
	</tr>

	<tr>
		<td colspan="3"><p>{$trans.playlist_help}</p></td>
	</tr>
</table>
</div>

<div class="unit">
<form action="index.php?page=playlist&amp;action=ssv" method="post">
<input type="hidden" name="auth" value="{$playlist_auth_key}" />

<table>
	<tr>
		<td colspan="3"><h3>{$trans.ssv}</h3></td>
	</tr>

	

	<tr>
		<td class="left">{$trans.give_name}</td>
		<td><input type="text" name="name" value="" id="give_name"></td>
		<td class="right">{$trans.name_help}</td>
	</tr>

	<tr>
		<td class="left">{$trans.which_postings}</td>
		<td><input type="text" name="ssv" value="" id="ssv" /></td>
		<td class="right">{$trans.ssv_help}</td>
	</tr>

	<tr>
		<td></td>
		<td></td>
		<td class="right"><input type="submit" value="{$trans.make}"></td>
	</tr>
	
</table>

</form>
</div>


<div class="unit">
<form action="index.php?page=playlist&amp;action=cat" method="post">
<input type="hidden" name="auth" value="{$playlist_auth_key}" />
<table>
	<tr>
		<td colspan="3"><h3>{$trans.cat}</h3></td>
	</tr>

	<tr>
		<td class="left">{$trans.give_name}</td>
		<td><input type="text" name="name" value="" id="give_name"></td>
		<td class="right">{$trans.name_help}</td>
	</tr>

	<tr>
		<td class="left">{$trans.limit}</td>
		<td><input type="text" name="limit" value="5" id="limit" /></td>
		<td class="right">{$trans.limit_help}</td></tr>

	<tr>
		<td class="left">{$trans.which_cat}</td>
		<td>
			<select name="category">
			<option value = '0' selected="selected">{$trans.all_cats}</option>
			{foreach from=$categories item=category}
			<option value = '{$category.id}'>{$category.name}</option>
			{/foreach}
			</select>
		</td>
		<td class="right"></td>
	</tr>
	
	<tr>
		<td class="left"></td>
		<td></td>
		<td class="right"><input type="submit" value="{$trans.make}"></td>
	</tr>
	
</table>
</form>

</div>

{literal}<script>
function selectItem(li) {
}

function formatItem(row) {
	return row;
}

$(document).ready(function() {
	$("#tag").autocomplete('index.php?page=autocomplete&type=tags', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1 });
});
</script>{/literal}


<div class="unit">
<form action="index.php?page=playlist&amp;action=tag" method="post" onsubmit="getNameAndLimit('tag')">
<table>
	<tr><td colspan="3"><h3>{$trans.tag}</h3></td></tr>

	
	<input type="hidden" name="auth" value="{$playlist_auth_key}" />

	<tr>
		<td class="left">{$trans.give_name}</td>
		<td><input type="text" name="name" value="" id="give_name"></td>
		<td class="right">{$trans.name_help}</td>
	</tr>

	<tr>
		<td class="left">{$trans.limit}</td>
		<td><input type="text" name="limit" value="5" id="limit" /></td>
		<td class="right">{$trans.limit_help}</td>
	</tr>

	<tr>
		<td class="left">{$trans.which_tag}</td>
		<td><input id="tag" type="text" name="tag" value="" /></td>
		<td class="right"></td>	
	</tr>

	<tr>
		<td class="left"></td>
		<td></td>
		<td class="right"><input type="submit" value="{$trans.make}"></td>
	</tr>
	
</table>
</form>

</div>

<div class="unit">
<table>

	<tr>
		<td colspan="3"><h3>{$trans.in_audio}</h3></td>
	</tr>
	{foreach from=$audio_files item=file}
	<tr>
	
		<td class="left"><a href="{$url}/audio/{$file.filename}" target="_blank" title="{$trans.view_file}">{$file.name}</a></td>
		<td>{$file.type}</td>
		<td>
		{if !empty($file.id)}
		<form method="post" action="index.php?page=record2&amp;do=edit&amp;id={$file.id}">
		<input type="hidden" name="auth" value="{$record2_auth_key}" />
		<input type="submit" value="Find this posting!" />
		</form>
		{else}
		I cannot find a posting containing this playlist file. Do you want to delete the file from your audio folder?
		{/if}
		</td>
		
	</tr>
	{foreachelse}
	<tr>
		<td colspan="3">{$trans.none_found}</td>
	</tr>
	{/foreach}
</table>
</div>

<div class="unit">
<table>

	<tr>
		<td colspan="3"><h3>{$trans.in_upload}</h3></td>
	</tr>
	{foreach from=$upload_files item=file}
	<tr>
	<td class="left"><a href="{$url}/upload/{$file.filename}" target="_blank" title="View this file">{$file.name}</a></td>
		<td>{$file.type}</td>
		<td>
		<form method="post" action="index.php?page=record2&amp;do=transfer">
		<input type="hidden" name="auth" value="{$record2_auth_key}" />
		<input type="hidden" name="filename" value="{$file.filename}" />
		<input type="submit" value="Create posting now!" />
		</form>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="3">{$trans.none_found}</td>
	</tr>
	{/foreach}
</table>
</div>


</div><!-- close content  -->

{include file='manager_footer.tpl'}
