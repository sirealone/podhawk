{*   template for backend images  page   *}

{include file='manager_head.tpl'}

<body id="images">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	{if $ipage == 'list'}
	<h3>{$paging_string}</h3>
	{/if}	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>{/if}


{if $ipage == 'find'}

<div class="unit">
<table>
	<tr>
		<td colspan="3"><h3>{$trans.find_images}</h3></td>
	</tr>
	<form action="index.php?page=images&amp;action=list&amp;auth={$images_auth_key}" method="post">
		<input type="hidden" name="auth" value="{$images_auth_key}" />
	<tr>
		<td>{$trans.beginning}</td>
		<td>
			<input type="text" name="choose_1" value="" id="choose_1" />
		</td>
		<td>{$trans.blank}</td>
	</tr>
	<tr>
		<td>{$trans.uploaded}</td>
		<td>
			<select name="choose_2" id="choose_2" class="medium">
			<option value="1">{$trans.any_time}</option>
			<option value="2">{$trans.last_day}</option>
			<option value="3">{$trans.last_week}</option>
			<option value="4">{$trans.last_month}</option>
			</select>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="{$trans.show}" />
		</td>
		<td></td>
		<td></td>
	</tr>
	</form>
</table>
{if $repeat_search == true}
<br />
<p><a href="index.php?page=images&amp;action=list&amp;auth={$images_auth_key}">{$trans.back_list}</a></p>
{/if}
</div>

<div class="unit">
<table>
	<tr>
		<td colspan="3"><h3>{$trans.upload_images}</h3></td>
	</tr>
	<tr>
		<td style="width: 200px;">
			<input id="flashupload" name="fileupload" type="file" />
			<a href="javascript:$('#flashupload').uploadifyClearQueue();">Clear upload queue</a>
		</td>
		<td>
			<input type="button" style= "width: 80px;"value="Upload" onClick="$('#flashupload').uploadifyUpload();" />
		</td>
	</tr>

</table>
</div>

{if ($gif_supported == true) && ($jpg_supported == true) && ($png_supported == true)}
<p class="msg">{$trans.support_all}</p>
{else}<p>{$trans.not_support_all} :</p>
	{if $gif_supported == false} gif{/if}
	{if $jpg_supported == false} jpg{/if}
	{if $png_supported == false} png{/if}
{/if}

<p class="msg"><script language="javascript" type="text/javascript">
<!--
document.write('{$trans.js_enabled}');
//-->
</script><noscript>{$trans.js_not_enabled}</noscript></p> 

{elseif $ipage == 'list'}

<table>
{foreach from=$images item=image}
	<tr onmouseover="$(this).css('background-color', 'white');" onmouseout="$(this).css('background-color', {if $warning == true}'#FFEFEF'{else}'#F4F4E3'){/if};">
		<td><a href="../images/{$image.name|escape:'url'}" rel="lightbox[images]" title="{$image.name}<br />{$trans.width}: {$image.width} px<br />{$trans.height}: {$image.height} px<br />{$trans.size}: {$image.size} kB<br />{$trans.uploaded_date} {$image.uploaded|date_format}" ><img src="timthumb/timthumb.php?src=images/{$image.name|escape:'url'}&w=30&h=30&zc=1" alt="" title="{$trans.click_view} {$image.name}" /></a></td>
	   	<td>
		<form>
			<input type="button" value="Details" onclick="alert('{$trans.path_from_root}: images/{$image.name}\n{$trans.width}: {$image.width} px\n{$trans.height}: {$image.height} px\n{$trans.size}: {$image.size} kB\n{$trans.uploaded_date} {$image.uploaded|date_format}')" />
			<input type="hidden" name="auth" value="{$images_auth_key}" />
		</form>
		</td>
	   	<td>
		<form action="index.php?page=images&amp;action=delete" method="post" onSubmit="return yesno('{$trans.confirm_delete}\n {$image.name}')">
			<input type="hidden" name="auth" value="{$images_auth_key}" />
			<input type="hidden" name="del_image" value="{$image.name}" />
			<input type="submit" value="{$trans.delete}" />
		</form>
		</td>
		<td>
		<form action="index.php?page=images&amp;action=rename_show" method="post">
			<input type="hidden" name="auth" value="{$images_auth_key}" />
			<input type="hidden" name="rename_image" value="{$image.name}" />
			<input type="submit" value="{$trans.rename}" />
		</form>
		</td>
		<td>
		<form action="index.php?page=images&amp;action=change_size_show" method="post">
			<input type="hidden" name="auth" value="{$images_auth_key}" />
			<input type="hidden" name="imageToResize" value="{$image.name}" />
			<input type="submit" value="{$trans.resize}" />
		</form>
		</td>
		<td>
		<form action="index.php?page=images&amp;action=make_tag_show" method="post">
			<input type="hidden" name="auth" value="{$images_auth_key}" />
			<input type="hidden" name="imageToHTML" value="{$image.name}" />
			<input type="submit" value="{$trans.make_tag}" />
		</form>
		</td>
	</tr>
{/foreach}
</table>

<p class="msg"><a href="index.php?page=images">{$trans.back_find}</a></p>

{elseif $ipage == 'new_name'}

<form action="index.php?page=images&amp;action=rename_do" name="rename" method="post" onSubmit="return newNameValid()">
	<input type="hidden" name="auth" value="{$images_auth_key}" />
	<h3>{$trans.rename_image} {$image}</h3>
	<table>
	<tr>
		<td>{$trans.image_new_name} : </td>
		<td><input type="text" name="newimagename" value="" />.{$ext}
   			<input type="hidden" name="oldimagename" value="{$image}" />
   			{*<input type="hidden" name="dirname" value="{$imginfo.dirname}/" />
   			<input type="hidden" name="ext" value="{$imginfo.extension}" />*}
		</td>
	</tr>
	<tr>
		<td>{$trans.retain_copy}</td>
		<td>
			<input type="checkbox" name="retainCopy" value="1" />
		</td>
	</tr>	
	<tr>
		<td><input type="submit" value="{$trans.submit_name}" /></td>
		<td></td>
	</tr>
	</table>
	</form>

<p class="msg"><a href="index.php?page=images&amp;action=list&amp;auth={$images_auth_key}">{$trans.back_list}</a></p>

{elseif $ipage == 'new_size'}

<h3>{$trans.resize} {$imagedata.name}</h3>
   <p>{$trans.pres_dims} - {$trans.width} {$imagedata.width} px; {$trans.height} {$imagedata.height} px</p>
   <form action="index.php?page=images&amp;action=resize" method="post" name="formResize" onSubmit="return checkImageResizeData()">
		<input type="hidden" name="auth" value="{$images_auth_key}" />
		<table>
			<tr>
				<td>{$trans.enter_width}<br />{$trans.enter_width_2}</td>
  				<td><input type="text" name="newWidth" /> px</td>
   				<td>
					<input type="hidden" name="inputRatio" value="{$imagedata.ratio}" />
					<input type="hidden" name="imageToResize" value="{$imagedata.name}" />
				</td>
			</tr>
   			<tr>
				<td>{$trans.resize_name} </td>
				<td><input type="text" name="newname" value="{$imagedata.nameWithoutExt}" />.{$imagedata.ext}</td>
				<td></td>
			</tr>
			<tr>
				<td>{$trans.retain_copy}<br/>{$trans.in_images}</td>
				<td>
					<input type="checkbox" name="retainCopy" value="1" />
				</td>
			</tr>	
   			<tr>
				<td><input type="submit" value="{$trans.resize}" /></td>
				<td></td>
			</tr>
		</table>
	</form>

<p class="msg"><a href="index.php?page=images&amp;action=list&amp;auth={$images_auth_key}">{$trans.back_list}</a></p>

{elseif $ipage == 'make_html'}

<h3>{$trans.make_html_tag_1}{$imageToHTML}{$trans.make_html_tag_2}</h3>
	
	<form action="index.php?page=images&amp;action=maketag" name="taginfo" method="post">
		<input type="hidden" name="auth" value="{$images_auth_key}" />

	<table>
		<tr>
			<td class="help" colspan="3">{$trans.lightbox_question}<br />{$trans.lightbox_question_2}
		<tr>
			<td>{$trans.lightbox_question_3}</td>
			<td>
				<input type="radio" name="lightbox" value="1" />{$trans.yes}&nbsp;&nbsp;
				<input type="radio" name="lightbox" checked="checked" value="0" />{$trans.no}
				<input type="hidden" name="imageToHTML" value="{$imageToHTML}" />
			</td>
			<td class="help">{$trans.lightbox_yes_1} {$imageToHTML} {$trans.lightbox_yes_2}<br />{$trans.lightbox_no_1} {$imageToHTML} {$trans.lightbox_no_2}</td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.lightbox_caption}</td>
			<td>
				<textarea name="lightbox_caption" rows="5" cols="25" ></textarea>
			</td>
			<td class="help"></td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.lightbox_slideshow}</td>
			<td>
				<input type="text" name="lightbox_slideshow" value="" />
			</td>
			<td class="help">{$trans.slideshow_help}</td>
		</tr>			
		<tr class="lightbox">
			<td>{$trans.thumbnail_size}</td>
			<td>
				<input type="text" name="lightbox_size" value="200" /> pixels
			</td>
			<td class="help"></td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.thumbnail2}</td>
			<td>
				<input type="radio" name="lightbox_axis" value="width" />{$trans.width}&nbsp;&nbsp;
				<input type="radio" name="lightbox_axis" checked="checked" value="height" />{$trans.height}&nbsp;&nbsp;
				<input type="radio" name="lightbox_axis" value="square" />{$trans.square}
			</td>
			<td class="help">{$trans.thumbnail_help}</td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.thumbnail_caption}</td>
			<td>
				<textarea name="lightbox_webpage_caption" rows="5" cols="25" ></textarea></td>
			</td>
			<td class="help">{$trans.thumbnail_caption_help}</td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.thumbnail_position}</td>
			<td><select name="lightbox_webpage_align" class="medium">
				<option value="1">{$trans.align_left}</option>
				<option value="2">{$trans.align_centre}</option>
				<option value="3">{$trans.align_right}</option>
				<option value="4">{$trans.hide_image}</option>
				<option value="0">{$trans.do_nothing}</option>
				</select>
			</td>
			<td class="help">{$trans.thumbnail_position_help}</td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.thumbnail_class}</td>
			<td>
				<input type="text" name="lightbox_webpage_class" value="" />
			</td>
			<td class="help">{$trans.thumbnail_class_help}</td>
		</tr>
		<tr class="lightbox">
			<td>{$trans.thumbnail_border}</td>
			<td>
				<input type="radio" name="lightbox_webpage_border" checked="checked" value="1" />{$trans.border}&nbsp;&nbsp;
				<input type="radio" name="lightbox_webpage_border" value="0" />{$trans.no_border}
			</td>
			<td class="help">{$trans.thumbnail_border_help}</td>
		</tr>	
		{* end lightbox *}
		{* no lightbox *}
		
   		<tr class="no_lightbox">
			<td>{$trans.url_type}</td>
			<td><select name="urltype" class="medium">
				<option value="absolute">{$trans.absolute}</option>
				<option value="relative">{$trans.relative}</option>
				</select>
			</td>
			<td class="help">{$trans.url_type_help}</td>
		</tr>
		<tr class="no_lightbox">
			<td>{$trans.optional} Title = </td>
			<td><input type="text" name="title" value="" /></td>
			<td class="help">{$trans.title_help}</td>
		</tr>
		<tr class="no_lightbox">
			<td>{$trans.optional} {$trans.link_to} http://</td>
			<td><input type="text" name="link" class="wide" value="" /></td>
			<td class="help">{$trans.link_help}</td>
		</tr>
		<tr class="no_lightbox">
			<td>{$trans.optional} {$trans.caption} </td>
			<td><textarea name="caption" rows="5" cols="25" ></textarea></td>
			<td class="help">{$trans.caption_help}</td>
		</tr>
		<tr class="no_lightbox">
			<td>{$trans.where}</td>
			<td><select name="align" class="medium">
				<option value="1">{$trans.align_left}</option>
				<option value="2">{$trans.align_centre}</option>
				<option value="3">{$trans.align_right}</option>
				</select>
			</td>
			<td></td>
		</tr>
		<tr class="no_lightbox">
			<td></td>
			<td>
				<input type="radio" name="border" checked="checked" value="1" />{$trans.border}&nbsp;&nbsp;
				<input type="radio" name="border" value="0" />{$trans.no_border}
			</td>
			<td></td>
		</tr>
		{* end no lightbox *}
		<tr>
			<td><input type="submit" value="{$trans.make_tag}" /></td>
			<td></td>
		</tr>
		
	</table>
	</form>

<p class="msg"><a href="index.php?page=images&amp;action=list&amp;auth={$images_auth_key}">{$trans.back_list}</a></p>

{elseif $ipage == 'display_tag'}

	<h3>HTML TAG</h3>
 	<p>{$trans.tag_here}</p><br />
	<textarea id="html_tag" onClick="selectAllText('html_tag')" >{$displayTag}</textarea>
	<br />
 	<p>{$trans.tag_here_help}</p><br />
		
	<p class="msg"><a href="index.php?page=images&amp;action=list&amp;auth={$images_auth_key}">{$trans.back_list}</a></p>
{/if} {*  close 'ipage ==' condition  *}

</div><!-- close content  -->

{include file='manager_footer.tpl'}
