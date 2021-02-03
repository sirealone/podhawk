 {*    template for id3 page    *}
{include file='manager_head.tpl'}

<body id="id3">


<div id="wrapper"{if $warning == true} class="warning"{/if}>
<h2>{if $audio_type == 1}{$trans.create}{elseif $audio_type == 3}Vorbis Comment Tags{/if}</h2>
<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>
<p class="msg">{$warnings}</p>

<form action="index.php?page=id3&amp;do=save&amp;id={$edit_id}" accept-charset="utf-8" method="post" enctype="multipart/form-data">
<input type="hidden" name="auth" value="{$id3_auth_key}" />

<table summary="ID3 tags of this audio posting">

<tr>

<td class="text">{$trans.title}</td>
<td><input type="text" name="id3title" value="{$data.title}" id="id3title"{$disabled} />
{if !empty($posting_title)}<br /><span id="use_title">Import posting title?</span>{/if}</td>
      
<td class="right text">{$trans.artist}:</td>
<td class="right"><input type="text" name="id3artist" value="{$data.artist}"{$disabled} /></td>

</tr>
<tr>

<td class="text">{$trans.album}:</td>
<td><input type="text" name="id3album" value="{$data.album}"{$disabled} /></td>
      
<td class="right text">{$trans.year}:</td>
<td class="right"><input type="text" name="id3year" value="{$data.year}"{$disabled} /></td>

</tr>
<tr>

<td class="text">{$trans.track}:</td>
<td><input type="text" name="id3track" value="{$data.track}"{$disabled} /></td>
      
<td class="right text">{$trans.genre}:</td>
<td class="right"><input type="text" name="id3genre" value="{$data.genre}"{$disabled} /></td>

</tr>
<tr>
      
<td class="text">{$trans.comment}:</td>
<td><textarea name="id3comment" id="id3comment"{$disabled}>{$data.comment}</textarea>
{if !empty($posting_summary)}<br /><span id="use_summary">Import posting summary?</span>{/if} </td>
     
{*  the image attachment  *}
{if $audio_type == 1}  
	<td class="right text">{$trans.image}:</td>

	<td class="right">
	{if $image_tag == true}
	<img src="../audio/temp_image{$data.imgtype}" width="100" />
	{else}{$trans.no_image}
	{/if}
	<div class="help">{$trans.imagehelp}</div>

	<br />

	{*  we can upload an image with the browser  *}
	<div class="help">{$trans.browser_upload}</div>
	<input id="imagechooser" type="file" name="image" accept="image/*" />


	{* or we can attach an image in the image directory to the file  *}
	<div class="help">{$trans.images_folder}</div>
	<input id="suggest" type="text" name="image_folder" value="" />

	</td></tr>
{/if}

</table>
<div id="save"><input type="submit" value="{$trans.updatebutton}"{$disabled} />
<input onClick="window.close();" type="submit" value="{$trans.close}" /></div>
</form>
<p><a href="index.php?page=all_id3_info&amp;id={$smarty.get.id}" target="_blank">All available id3 information about this audio file</a></p>
</div>
</body>
</html>
