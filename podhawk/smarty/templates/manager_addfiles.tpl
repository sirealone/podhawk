 {*    template for addfiles page    *}
{include file='manager_head.tpl'}

<body id="addfiles">


<div id="wrapper"{if $warning == true} class="warning"{/if}>
<h2>{$trans.create} "{$posting.title}"</h2>
<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>
<p class="msg">{$warnings}</p>

<p>{$trans.help1}</p><p>{$trans.help2}</p>
<h4>{$trans.upfolder}</h4>
<form action="index.php?page=addfiles&amp;id={$posting.id}&amp;action=addFile" method="post">
<input type="hidden" name="auth" value="{$addfiles_auth_key}" />
<select name="fileToAdd" class="leftInput"{if $addfilesAllowed == false} disabled="disabled"{/if} >
	<option value="" selected="selected">{$trans.choose_file}</option>
	<option value="">--------</option>
	{foreach from=$upload item=file}
		<option value="{$file|escape:'url'}">{$file}</option>
	{/foreach}
</select>
<input type="submit" value="Attach to posting" class="rightInput"{if $addfilesAllowed == false} disabled="disabled"{/if} />
</form>
<h4>{$trans.attached_files}</h4>
{if $addfilesAllowed == false}
	<p>{$trans.attach_fail}</p>
{else}
	{if !empty($posting.addfiles)}
		<p>{$trans.these_files_attached}:</p>
		<table>
		{foreach from=$posting.addfiles item=file}
		<tr>
			<td class="leftInput">{$file.name}</td>
			<td>
				<form action="index.php?page=addfiles&amp;action=removeFile&amp;id={$posting.id}" method="post">
				<input type="hidden" name="auth" value="{$addfiles_auth_key}" />
				<input type="hidden" name="fileToRemove" value="{$file.name}" />
				<input type="submit" value="{$trans.file_remove}" class="rightInput" />
				</form>
			</td>
		</tr>
		{/foreach}
		</table>
	{else}
		<p>{$trans.no_addfiles}</p>
	{/if}
{/if}
<h4>{$trans.attached_image}</h4>
<p>{$trans.attached_image_help}</p>

{if !empty($posting.image)}
<p>{$trans.these_images_attached}</p>
	{* thumbnail, with lightbox *}
<table>
	<tr>
	<td class="leftInput">
	<a href="../images/{$posting.image|escape:'url'}" rel="lightbox" title="{$posting.image}">
	<img src="timthumb/timthumb.php?src=../images/{$posting.image|escape:'url'}&w=80&h=80&zc=1" alt="" title="{$trans.click_view} {$posting.image}" class="poster" />
	</a>
	</td>
	<td class="rightInput">
	<form action="index.php?page=addfiles&amp;action=removeImage&amp;id={$posting.id}" method="post">
		<input type="hidden" name="auth" value="{$addfiles_auth_key}" />
		<input type="submit" value="{$trans.remove_image}" />
	</form>
	</td>
	</tr>
</table>
{else}
<p>{$trans.no_attached_images}</p>
<form action="index.php?page=addfiles&amp;id={$posting.id}&amp;action=addImage" method="post">
<input type="hidden" name="auth" value="{$addfiles_auth_key}" />
<input id="suggest" type="text" name="imageToAdd" value="{$trans.start_typing}" class="leftInput" />
<input type="submit" value="{$trans.add_image}" class="rightInput" />
</form>
{/if}
<button type="button" value="close" id="closeButton">{$trans.close_window}</button>
</div> {* close wrapper *}
</body>
