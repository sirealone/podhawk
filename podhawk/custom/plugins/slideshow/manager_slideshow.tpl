{* template for slideshow page  *}

{include file='standard:manager_head.tpl'}

<body id="slideshow">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>	
	</div> <!-- close header -->

{include file='standard:menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{$trans.$message|default:$message}</p>{/if}

<p class="intro">{$trans.intro}</p>

<form action="index.php?page=slideshow&amp;action=makeslideshow" method="post" id="slideshow_form">
<input type="hidden" name="auth" value="{$slideshow_auth_key}" />
<input type="hidden" name="saveTo" value="{$saveTo}" />
<input type="hidden" name="saveFileName" value="{$slideshowFileName}" />
<table>
<tr>
	<td class="left">{$trans.slideshow_name}</td>
	<td class="center">
		<input type="text" value="{$slideshow.name|default:''}" name="name" />
	</td>
	<td class="right">{$trans.slideshow_name_help}</td>
</tr>

<tr id="thumbs_radio_buttons">
	<td class="left">{$trans.thumb_or_text}</td>
	<td class="center">
		<input type="radio" name="thumbs" {if ($slideshow.thumbs == '0')}checked="checked" {/if}value="0" />{$trans.text_link}<br />
		<input type="radio" name="thumbs" {if ($slideshow.thumbs == '1')}checked="checked" {/if}value="1" />{$trans.thumb1}<br />
		<input type="radio" name="thumbs" {if ($slideshow.thumbs == '2')}checked="checked" {/if}value="2" />{$trans.thumball}<br />
	</td>
	<td class="right">{$trans.thumb_text_help}</td>
</tr>

<tr class="textlink">
	<td class="left">{$trans.txt}:</td>
	<td class="center">
		<input type="text" name="textlink" value="{$slideshow.textlink|default:'Click to start slideshow'}" />
	</td>
	<td class="right">{$trans.txt_help}</td>
</tr>
	
<tr class="thumblink">
	<td class="left">{$trans.thumb_size}:</td>
	<td class="center">
		<input type="text" class="narrow" value="{$slideshow.thumbsize|default:80}" name="thumbsize" />pixels
	</td>
	<td class="right"></td>
</tr>

<tr class="thumblink">
	<td class="left">{$trans.thumb_width_height}</td>
	<td class="center">
		<input type="radio" name="axis" {if ($slideshow.axis == 'width')}checked="checked" {/if}value="width" /> {$trans.width}&nbsp;&nbsp;
		<input type="radio" name="axis" {if ($slideshow.axis == 'height')}checked="checked" {/if}value="height" /> {$trans.height}&nbsp;&nbsp;
		<input type="radio" name="axis" {if ($slideshow.axis == 'square')}checked="checked" {/if}value="square" /> {$trans.square}
	</td>
	<td class="right">{$trans.width_height_help}</td>
</tr>

<tr>
	<td colspan="3" class="right" id="image_row_help">{$trans.image_row_help}</td>
</tr>
<tr>
	<th>{$trans.name_web}</th>
	<th>Caption (optional)</th><th></th>
</tr>

{if !empty($slideshow.images)}
	{foreach from=$slideshow.images item=image name=slideshow_loop}
	<tr class="image_rows">
		<td class="left">
			<input type="text" name="image[]" value="{$image.name}" class="suggest" />
		</td>
		<td class="center">
			<textarea name="caption[]" value="{$image.caption}" rows="5" cols="25">{$image.caption|default:''}</textarea>
		</td>
		<td><button type="button" class="remove" value="Remove">{$trans.remove_image}</button></td>
		</td>
	</tr>
	{/foreach}
{else}
	<tr class="image_rows">
		<td class="left">
			<input type="text" name="image[]" value="" class="suggest" />
		</td>
		<td class="center">
			<textarea name="caption[]" value="" rows="5" cols="25"></textarea>
		</td>
		<td>
			<button type="button" class="remove" value="Remove">{$trans.remove_image}</button>
		</td>
	</tr>
{/if}
<tr> <td></td><td></td><td><button type="button" id="addRow">{$trans.add_row}</button></td></tr>
<tr class="submit_row"> <td><button type="button" id="runSlideshow">{$trans.preview_slideshow}</button></td><td></td><td><input type="submit" name="submit" value="{$trans.create_slideshow}" /></td></tr>

</table>
{* clickable list of slideshow files in upload folder *}

<div id="upload_folder_contents">
<hr />
<h3>{$trans.upfolder}</h3>
{if !empty($upload_folder)}
<p>{$trans.upfolder_contents} :</p>
<table>
	{foreach from=$upload_folder item=file}
	<tr>
		<td class="left">{$file}</td>
		<td><a href="index.php?page=slideshow&amp;action=retrieve_from_upload&amp;filename={$file}&amp;auth={$slideshow_auth_key}">{$trans.file_edit}</a></td>
		<td><a href="index.php?page=slideshow&amp;action=delete_from_upload&amp;filename={$file}&amp;auth={$slideshow_auth_key}">{$trans.file_delete}</a></td>
	</tr>
	{/foreach}
</table>
{else}
<p>{$trans.upload_empty}</p>
{/if}
</div>


<div id="slideshow_files_in_posts">
<hr />
<h3>{$trans.slide_posts}</h3>
{if !empty($slideshow_posts)}
<p>{$trans.slideposts_links} :</p>
<table>
	{foreach from=$slideshow_posts item=slideshow_post}
	<tr>
		<td class="left">{$trans.post_title} : {$slideshow_post.title}<br />{$trans.slidename} : {$slideshow_post.slideshow_name}<br />{$trans.file_name} : {$slideshow_post.audio_file}</td>
		<td><a href="index.php?page=record2&amp;do=edit&amp;id={$slideshow_post.id}&amp;auth={$record2_auth_key}">{$trans.post_edit}</a></td>
		<td><a href="index.php?page=slideshow&amp;action=retrieve_from_posting&amp;filename={$slideshow_post.audio_file}&amp;auth={$slideshow_auth_key}">{$trans.slideshow_edit}</a></td>
	</tr>
	{/foreach}
</table>
{else}
<p>{$trans.no_slideposts}</p>
{/if}
</div>
	
</div> {* close 'content' *}

{include file='standard:manager_footer.tpl'}
