   {*   template for recording page 1   *}

{include file='manager_head.tpl'}

<body id="record1">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	<h3>{$trans.create_desc}</h3>
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
	<div id="method">
	<div id="upload">
{if !empty($message)}
<p class="msg">{$trans.$message|default:$message}</p><br />
{/if}


<div class="unit">
<h2>{$trans.upload_a_file}</h2>
	<div class="sub_unit"></div>
{if $flash_uploader == true}  {*  flash file uploader   *}		

<table>
<tr>
	<td class="col1"><h3>{$trans.flash_uploader}<br /><small>(max {$upload_limit|getmegabytes} MB)</small></h3></td>
	<td class="col2"><input id="flashupload" name="fileupload" type="file" /><a href="javascript:$('#flashupload').uploadifyClearQueue();">{$trans.clear_queue}</a></td><td><input type="button" value="{$trans.upload_files}" onClick="$('#flashupload').uploadifyUpload();" /></td>
</tr> 
    	
</table>

{else} {* use classic http file upload *}

<table>
<form method="post" action="index.php?page=record2&amp;do=browser{if $update == true}&amp;id={$update_id}{/if}" enctype="multipart/form-data" onSubmit="return saythis('{$trans.alert_patience}')">
	<input type="hidden" name="auth" value="{$record2_auth_key}" />
<tr>
	<td class="col1"><h3>Upload file<br /><small>(max {$upload_limit|getmegabytes} MB)</small></h3></td>
	<td class="col2"><input name="fileupload" type="file" /></td>
	<td><input type="submit" value="Upload file" /></td>
</tr>
</form>
</table>
{/if}
			

{*  upload by Z-upload ftp client  *}	
{if $ftp == true}

	<div class="sub_unit"></div>	
	{if $ftp_details == false}
	<p class="msg">{$trans.ftp_warning}</p>
	{/if}
	<table>
	<tr><td class="col1"><h3>{$trans.ftp}</h3><td>
 	<td><a href="index.php?page=javaload{if isset($smarty.get.id)}&amp;id={$smarty.get.id}{/if}&amp;auth={$javaload_auth_key}"
	 onclick= "link_popup(this,500,500); return false" "title="{$trans.zupload}"><input type="button" value="{$trans.ftp_button}" {if $ftp_details == false}disabled="disabled"{/if} /></a></td>
	
	<td></td></tr>
	</table>
	
{/if}

</div>  {* close class=unit  *}
</div>  {* close id=upload  *}

<div id="create">
<h2>{$trans.create_new_posting}</h2>
{*   new posting with no audio file   *}
		<div class="unit">
	<table>
	<form method="post" action="index.php?page=record2&amp;do=nofile
	{if $update==true}&amp;id={$update_id}{/if}">
	<input type="hidden" name="auth" value="{$record2_auth_key}" />
	<tr>
		<td class="col1"><h3>{$trans.no_audio}</h3></td>
		<td class="col2">{$trans.make_later}</td>
		<td><input id="butt_nofile" type="submit" value="{$trans.next_step}" /></td>
	</tr>
	
	</form>
	</table>
		<div class="sub_unit"></div>
{*  the upload folder *}	
	
		<div id="upload_message">
		<p class="msg">
		{if empty($upload)}{$trans.upload_empty}
		{elseif $upload|@count == 1}{$trans.upload_one}
		{else}{$trans.upload_many1}{$upload|@count}{$trans.upload_many2}
		{/if}</p>
	 	</div>
	<table>
	<form method="post" id="fromUpload" action="index.php?page=record2&amp;do=transfer{if $update == true}&amp;id={$update_id}{/if}">
	<input type="hidden" name="auth" value="{$record2_auth_key}" />
	<tr><td class="col1"><h3>{$trans.search_folder}</h3></td>
	<td class="col2" id="new_data">
		<select class="datainput" name="filename" id="upload_folder_contents">
			<option value="" selected="selected">{$trans.choose_file}</option>
			<option value="">--------</option>
			{foreach from=$upload item=file}
			<option value="{$file}">{$file}</option>
			{/foreach}
		</select>
	</td>
	<td>
		<input type="submit" id="submitFromUploadFolder" value="{$trans.get_file}" {if empty($upload)}disabled="disabled" {/if}/>
	</td></tr>
	</form>
	</table>

	<div class="sub_unit"></div>


{* get or link to a file from the web  *}
	
	<table>
	<form method="post" action="index.php?page=record2&amp;do=web{if $update == true}&amp;id={$update_id}{/if}">
	<input type="hidden" name="auth" value="{$record2_auth_key}" />
	<tr><td class="col1"><h3>{$trans.get_web}</h3></td>

	<td class="col2"><input onfocus="this.value='';" class="datainput" type="text" name="linkurl" value="{$trans.url_here}" /><br />
	
	<input type="radio" name="method" value="link" checked="checked" />{$trans.link_file}&nbsp;&nbsp;{if $url_fopen == true}<input type="radio" name="method" value="copy" />{$trans.copy_file}{/if}</td>
	<td><input type="submit" value="{$trans.get_file}"></td></tr>
	</form>
	</table>
	

{*   link to an external file to play in the JW player   *}

{if $jwplayer_installed == true}

	<div class="sub_unit"></div>
	
	<form method="post" action="index.php?page=record2&amp;do=jw_link{if $update == true}&amp;id={$update_id}{/if}">
	<input type="hidden" name="auth" value="{$record2_auth_key}" />

	<table>
	<tr><td class="col1"><h3>{$trans.jw_link}</h3><br />
	<p>{$trans.warning}</td>
	<td class="col2"><input onfocus="this.value='';" class="datainput" type="text" name="jwlinkurl" value="{$trans.url_here}" /><br />
	<p>{$trans.explain_url}</p>
	<input type="radio" name="jw_link_type" value="1" checked="checked" onchange="showStreamerData();" />{$trans.remote_flv}<br />
	<input type="radio" name="jw_link_type" value="2" onchange="showStreamerData();" />{$trans.yt}<br />
	<input type="radio" name="jw_link_type" value="3" onchange="showStreamerData();" />{$trans.playlist}<br />
		
	<input type="radio" name="jw_link_type" id="jwlink1" value="4" onchange="showStreamerData();" />{$trans.rtmp_single}<br />
	<input type="radio" name="jw_link_type" id="jwlink2" value="5" onchange="showStreamerData();" />{$trans.rtmp_playlist}<br />
	<input type="radio" name="jw_link_type" id="jwlink3" value="6" onchange="showStreamerData();" />{$trans.http_single}<br />
	<input type="radio" name="jw_link_type" id="jwlink4" value="7" onchange="showStreamerData();" />{$trans.http_playlist}
	</td><td><input type="submit" value="{$trans.get_file}"></td></tr>
	</table><br />

	<div id="streamerdata" class="hidden">
	<table>
	<tr><td class="col1"></td><td class="col2">
	<p>{$trans.streamer_help}</p>
	{$trans.streamer}<br /><input id="streamer" type="text" class="datainput" name="streamer" value="" /><br />
	{$trans.file_to_stream}<br /><input id="file_to_stream" type="text" class="datainput" name="file_to_stream" value="" />
	</td><td></td></tr>
	</table>
	</div> {* streamerdata  *}

	
	</form>
	
{/if}									

	
	</div>{*  unit  *}	
</div>{*  create  *}
</div> {* id=method *}
</div><!--  close content  -->
{include file='manager_footer.tpl'}
