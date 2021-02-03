    {*    template for recording page 2   *}
{include file='manager_head.tpl'}

<body id="record2">

{if $ping == true}
<script type="text/javascript">
window.open("index.php?page=ping&ping={$ping}&id={$posting.id}","ping","width=450,height=250,scrollbars=yes");</script>
{/if}

{* background colour of wrapper depends on posting status *}
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	<h3>{$posting.title} : id = {$posting.id} {if !empty($edit_date)}: {$trans.lastedited}{$edit_date|date_format:"%d %b %Y %H:%M"} {$trans.using} {$edited_with}{/if}</h3>
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
<p class="msg" id="message">{$trans.$message|default:$message}</p>
{if isset($message2)}
	<p class="message">{$trans.$message2|default:$message2}</p>
{/if}
<form action="index.php?page=record2&amp;do=save&amp;id={$id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="auth" value="{$record2_auth_key}" />

<div id="leftcolumn">

	<h3>{$trans.title}</h3>
	<input id="title" type="text" name="title" {$readonly} value="{$posting.title|urldecode}" />
	{if $may_edit == false} {* we need the title, even if the user cannot edit *}
		<input id="title" type="hidden" name="title" value="{$posting.title|urldecode}" />
	{/if}

	<h3>{$trans.message}</h3>
	
	<textarea {$readonly} name="message" id="txtarea" class="ed">{if $posting.edited_with == 2 || $posting.edited_with == 3}{$posting.message_html}{else}{$posting.message_input|trim}{/if}</textarea>

	<input type="hidden" name="editor_used" value="{$editor_to_use}" />

	<h3>{$trans.summary}</h3>
	<textarea {$readonly} name="summary" id="summary">{$posting.summary}</textarea>

	<h3>{$trans.tags}</h3>
	<input id="suggest" type="text" class="tags" name="tags" {$readonly} value="{$posting.tags}" />


	<h3>{$trans.cats}</h3>
	{foreach from=$this_cats item=this_cat name=this_cats}
		<select class="category" {$readonly} name="cat{$smarty.foreach.this_cats.iteration}">
		<option value="0">---</option>
			{foreach from=$cats item=cat name=catloop}	
			   <option value="{$cat.id}"{if $this_cat == $cat.id} selected="selected"{/if}>{$cat.name}</option>
			{/foreach}
		</select>
	{/foreach}

	
		<h3>{$trans.author}</h3>
		<select name="author" {$readonly2} class = "author">
		{foreach from=$authors key=author_id item=author}			
		<option value="{$author_id}"{if $author_id == $my_id} selected="selected"{/if}>{$author.nickname}</option>			
		{/foreach}
		</select>

</div> {*  close leftcolumn  *}

<div id="rightcolumn">

	{*  submit button   *}
	{if $may_edit == true || $may_publish == true}
		{if $posting.amazonAvailable == true}
			<div class="submit">
			<input type="hidden" name="filelocal" value="{$posting.filelocal}" />
			<input type="hidden" name="audio_file" value="{$posting.audio_file}" />
			<button type="submit" name="amazon_upload" class="amazon_upload" {if ($posting.filelocal == true)}onclick="showAmazonMessage('up')"{/if}>{$trans.amazon_1}</button>
			<button type="submit" name="amazon_download" class="amazon_download"{if ($posting.filelocal == false)}onclick="showAmazonMessage('down')"{/if}>{$trans.amazon_2}</button>
			</div>
		{else}
			<div class="submit">
			<input class="save wide" type="submit" value="{$trans.saveall}" />
			</div>
		{/if}
	{/if}


	{if $posting.audio_file_to_show == true}

		<h3>{$trans.audio}</h3>
		{if $posting.filelocal == true}
	
		 	{include file='manager_flashplayer.tpl'}
	
		 	<table id="audiodata">
			<tr><td>{$trans.filename}</td><td><a href="{$posting.audio_link}">{$posting.audio_file}</a></td></tr>
			<tr><td>{$trans.sizedur}</td><td>{$id3.size|getmegabytes} MB / {$id3.duration} {$trans.mins}</td></tr>
			<tr><td>{$trans.qual}</td><td>{$id3.bitrate/1000} kb/s ({$id3.bitrate_mode|strtoupper}) / {$id3.sample_rate/1000} kHz / {$id3.channelmode}</td></tr>
			<tr><td>{$trans.id3}</td><td>{$id3.title} {$id3.track}</td></tr>
			</table>

			{* audio/video file information *}		
			<input href="{if $posting.audio_type == 1 || $posting.audio_type == 3}index.php?page=id3&amp;id={$posting.id}&amp;auth={$id3_auth_key}{else}index.php?page=all_id3_info&amp;id={$posting.id}{/if}" class="audiobutton" value="Audio/video tags" type="button" onClick="link_popup(this,850,450); return false" />
		
				{if $may_edit == true}
				<input class="audiobutton right" value="{$trans.change_audio}" type="button" onClick="self.location.href='index.php?page=record1&amp;do=update&amp;id={$posting.id}'" />
				{/if}
			<input type="hidden" name="audio_length" value="{$posting.audio_length}" />
			<input type="hidden" name="audio_size" value="{$posting.audio_size}" />

		{else}  {*  ie if the file is not local    *}

			{include file='manager_flashplayer.tpl'}

			<table id="audiodata">
			<tr><td><a href="{$posting.audio_file}">{$posting.audio_file|wordwrap:50}</a></td></tr>
			</table>

			<div id="non_local_data">
			<h3>{$trans.size}</h3>	
			<input {$readonly} type="text" name="audio_size" value="{$posting.audio_size|getmegabytes} MB" />
	
			<h3>{$trans.dur}</h3>
			<input {$readonly} type="text" name="audio_length" value="{$posting.audio_length} {$trans.secs}" />
			</div>

			{if $may_edit == true}
				<input class="audiobutton change" value="{$trans.change_audio}" type="button" onClick="self.location.href='index.php?page=record1&amp;do=update&amp;id={$posting.id}'" />
			{/if}
		{/if} {*  end 'file not local' section   *}

		{* button for attaching further files to the posting *}
		<input id="addfiles" value="{$trans.add_further_files}" type="button" />

	{else} {*    ie if there is no audio file     *}
	 
		<h3>{$trans.noaudiofile}</h3>
		<p>{$trans.audiolater}</p>
		<input type="hidden" name="audio_length" value="0" />
		<input type="hidden" name="audio_size" value="0" />
		<input type="hidden" name="audio_type" value="0" />

		{if $may_edit == true}
	 	<input class="audiobutton change" value="{$trans.addaudio}" type="button" onClick="self.location.href='index.php?page=record1&amp;do=update&amp;id={$id}'" />
		{/if}
	  
	{/if} {* close if_audio_file_to_show *}

	{*  comments   *}
	<div class="right_column_unit">
		<h3>{$trans.comments}</h3>
	{if $acceptcomments == 'none'}
		{$trans.acceptcomments}
		<input type="hidden" name="comment_on" value="{$posting.comment_on}" />
		<input type="hidden" name="comment_size" value="{$posting.comment_size}" />
	{else}
		<input {$readonly} class="radio" type="radio" name="comment_on" value="1" {if $posting.comment_on == 1} checked="checked"{/if} />{$trans.on}&nbsp;&nbsp;
		<input {$readonly} class="radio" type="radio" name="comment_on" value="0" {if $posting.comment_on == 0} checked="checked"{/if} />{$trans.off}&nbsp;&nbsp;
		<input {$readonly} class="radio" type="radio" name="comment_on" value="2" {if $posting.comment_on == 2} checked="checked"{/if} />{$trans.closed}
		</div>

		{* maximum size of audio comment *}
	
		<div class="right_column_unit">
		<h3>{$trans.sizelimit}</h3>
		<select {$readonly} name="comment_size">
			{foreach from=$comment_size item=item key=key}
			<option {$readonly} value="{$key}" {if $key == $posting.comment_size} selected {/if}>{$item}</option>
			{/foreach}
		</select>
	{/if}
	</div>

	{*   status    *}
	<div class="right_column_unit">
		<h3>{$trans.status}</h3>
		<input type="radio" name="status" value="1" {if $posting.status == 1} checked="checked"{/if} {$readonly4} />{$trans.draft}&nbsp;&nbsp;
		<input type="radio" name="status" value="2" {if $posting.status == 2} checked="checked"{/if} {$readonly4} />{$trans.finished}&nbsp;&nbsp;
		<input type="radio" name="status" value="3" {if $posting.status == 3} checked="checked"{/if} {$readonly3} />{$trans.onair}
	</div>

		    
	{*   preview   *}
	<div class="right_column_unit">
		<h3>{$trans.preview}</h3>
		<input class="radio" name="previews" type="radio" value="1" {if $preview == 1} checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
		<input class="radio" name="previews" type="radio" value="0" {if $preview == 0} checked="checked"{/if} />{$trans.no}

		{if $preview == 1}
		<p><a href="{$url}/index.php?id={$posting.id}&amp;preview=1" target="_blank">{$trans.preview_link}</a></p>
		{/if}
	</div>

</div>{*  close rightcolumn  *}

<div id="postsave">

	{*   posted date/time - sticky  *}
	<h3>{$trans.posttime}</h3>
	<div id="date">
		<input id="year" type="text" name="post1" maxlength="4" value="{$posted_date|date_format:'%Y'}" {$readonly} />
		
		<input type="text" name="post2" maxlength="2" value="{$posted_date|date_format:'%m'}" {$readonly} />
		
		<input type="text" name="post3" maxlength="2" value="{$posted_date|date_format:'%d'}" {$readonly} />
		
		<h4> {$trans.at} </h4>
		<input type="text" name="post4" maxlength="2" value="{$posted_date|date_format:'%H'}" {$readonly} />
		
		<h4>:</h4>
		<input type="text" name="post5" maxlength="2" value="{$posted_date|date_format:'%M'}" {$readonly} />
		
		<h4> {$trans.setnow} </h4>
		<input {$readonly} id="now" type="checkbox" name="now" />

		<h4> {$trans.sticky} </h4>
		<input {$readonly} id="sticky" type="checkbox" name="sticky" {if $posting.sticky == true} checked="checked"{/if} />

		<h4>{$trans.explicit}</h4>
		<input {$readonly} id="sticky" type="checkbox" name="itunes_explicit" {if $posting.itunes_explicit == true} checked="checked"{/if} />
	</div>
	
</div>  {*  close postsave  *}

{if $jwplayer_installed == true && $posting.playertype == 'jwvideo'}
	<div id="jw_extra_data">

		<h3>{$trans.jw_data}</h3>	
		<h4>{$trans.image} : </h4>
		<input type="text" name="image" value="{$posting.image}" />
		<p class="message">{$trans.image_message}</p>
		<h4>{$trans.link} : </h4>
		<input type="text" name="link" value="{$posting.link}" />
		<p class="message">{$trans.link_message}</p>

	</div>
{/if}

{*  plugins section 1  *}

{if !empty($posting.rec2_html1)}
	{foreach from=$posting.rec2_html1 item=html}
	{$html}
	{/foreach}
{/if}

{*    links   *}
<div id="hyperlinks">
	<table class="plain topspace">
	<tr>
		<th>{$trans.linkurl}</th>
		<th>{$trans.linkname}</th>
		<th>{$trans.linkdesc}</th>
	</tr>
	{section name=links_loop loop=$links_to_show}
	<tr>
		<td class="left">
			<input {$readonly} type="text" value="{$links[$smarty.section.links_loop.index].url|default:''}" name="linkurl{$smarty.section.links_loop.index}" />
		</td>
		<td class="center">
			<input {$readonly} type="text" value="{$links[$smarty.section.links_loop.index].title|default:''}" name="linktit{$smarty.section.links_loop.index}" />
		</td>
		<td class="right">
			<input {$readonly} type="text" value ="{$links[$smarty.section.links_loop.index].description|default:''}" name="linkdes{$smarty.section.links_loop.index}" />
		</td>
	</tr>
	{/section}
	</table>
</div>

{*  plugins section 2  *}

{if !empty($posting.rec2_html1)}
	{foreach from=$posting.rec2_html2 item=html}
	{$html}
	{/foreach}
{/if}

{*  submit button   *}
{if $may_edit == true}
		{if $posting.amazonAvailable == true}
			<div class="submit">
			<input type="hidden" name="filelocal" value="{$posting.filelocal}" />
			<input type="hidden" name="audio_file" value="{$posting.audio_file}" />
			<button type="submit" name="amazon_upload" class="amazon_upload">Save<br />to Amazon S3</button>
			<button type="submit" name="amazon_download" class="amazon_download">Save<br />to Audio Folder</button>
			</div>
		{else}
			<div class="submit">
			<input class="save wide" type="submit" value="{$trans.saveall}" />
			</div>
		{/if}
	{/if}

</form>

</div><!--   close content   -->

{include file='manager_footer.tpl'}
