   {*   template for backend players page   *}

{include file='manager_head.tpl'}

<body id="players">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{$trans.$message|default:$message}</p>{/if}

{if $admin == true}

<div class="unit">
<h2>{$trans.config_audio}</h2>
<br />

<form action="index.php?page=players&amp;do=save" method="post" name="form1">
<input type="hidden" name="auth" value="{$players_auth_key}" />
<table>

{*   choose the audio player  *}
<tr><td class="left">{$trans.choose_player}</td>
<td>
	<select {*onchange="showPlayerOptions(this)" *}name="audio_player_type" id="player_type">
	<option value="loudblog" {if $players.audio_player_type == 'loudblog'}selected="selected"{/if}>{$trans.loudblog}</option>
	<option value="emff" {if $players.audio_player_type == 'emff'}selected="selected"{/if}>{$trans.emff}</option>
	<option value="pixelout" {if $players.audio_player_type == 'pixelout'}selected="selected"{/if}>{$trans.pixelout}</option>
	{if $jw_player_installed == true}
	<option value="jwaudioplayer" {if $players.audio_player_type == 'jwaudioplayer'}selected="selected"{/if}>{$trans.jwaudioplayer}</option>
	{/if}
	</select>
</td><td><input type="submit" value="{$trans.saveset}" class="saveall" /></td></tr>
</table>

<div id="audio_player_type_loudblog">
<table>
<tr><td class="left">{$trans.your_player}</td>
<td>
<object type="application/x-shockwave-flash" data="custom/themes/{$theme}/emff.swf?src=backend/test.mp3" width="{$players.loudblog_width}" height="{$players.loudblog_height}">
    <param name="movie" value="custom/themes/{$theme}/emff.swf?src=backend/test.mp3" />
</object>
</td></tr>
</table>
</div>

{*   choose emff player   *}
<div id="audio_player_type_emff">

<table>
<tr><td class="left">{$trans.emff_choose}</td>
<td>
	<select name="emff_player" id="emffselect" onchange="showEmffPlayer();">
	{foreach from=$emff_players item=player}
	<option value="{$player}" {if $players.emff_player == $player}selected="selected"{/if}>{$player}</option>
	{/foreach}
	</select>
</td><td></td></tr>

<tr><td class="left">{$trans.emff_background}</td>
<td>
	<input name="emff_background" id="emffbackground" value="{$players.emff_background}" class="color {ldelim}pickerPosition:'top'{rdelim}" onchange="showEmffPlayer();" />
</td>
<td>
	<input type="checkbox" id= "emffstandard" name="emff_standard_background" value="true" {if $players.emff_standard_background == true}checked="checked"{/if} onchange="showEmffPlayer();" />{$trans.use_standard_background}
</td></tr>

<tr><td class="left">{$trans.your_player}</td>
<td id="showemff">
	<object type="application/x-shockwave-flash" data="custom/players/emff/emff_{$players.emff_player}.swf?src=backend/test.mp3" width="{$players.emff_width}" height="{$players.emff_height}">
	<param name="movie" value="custom/players/emff/emff_{$players.emff_player}.swf" />

	{if $players.emff_standard_background == false}
	<param name="bgcolor" value="#{$players.emff_background}" />
	{/if}	
	<param name="FlashVars" value="src=backend/test.mp3" />
	</object>
</td><td></td></tr>
</table>
</div>

{*   choose 1-Pixel_out player   *}
<div id="audio_player_type_pixelout">
<br />
<table id="pix_data">

<tr><td class="left">{$trans.your_player_pixelout}<br /><br /></td>
<td colspan="2">
	<p id="pixelout_player">Alternative content</p>
</td></tr>

<tr><td class="left">{$trans.pix_width}</td><td>
	<input name="pix_width" value="{$players.pix_width}" class="narrow" onchange="showPixeloutPlayer();" />
</td><td></td></tr>

<tr><td class="message">{$trans.click}</td><td></td><td></td></tr>

{foreach from=$pixelout_param_names item=name}
	<tr><td class="left">{$trans.$name}</td>
	<td>
		<input name="{$name}" id="{$name}" value="{$players.$name}" class="color {ldelim}pickerPosition:'right'{rdelim}" onchange="showPixeloutPlayer();" />
	</td>
	<td></td></tr>
{/foreach}


</table>
</div>

{*   configure jw audio player   *}
<div id="audio_player_type_jwaudioplayer">

{if $jw_player_installed == true}
	<table>
	<tr><td class="left">{$trans.jw_audio_width}</td>
	<td><input name="jw_audio_width" value="{$players.jw_audio_width}" class="narrow" id="jw_audio_width" onchange="showJwPlayer();" /></td><td></td></tr>
	<tr><td class="left">{$trans.jw_audio_height}</td>
	<td><input name="jw_audio_height" value="{$players.jw_audio_height}" class="narrow" id="jw_audio_height" onchange="showJwPlayer();" /></td><td></td></tr>
	<tr><td class="message" colspan="3">{$trans.jw_audio_colours}</td><td></td><td></td></tr>
	</table>
{/if}
</div>

</div> {*  close unit   *}

{*   configure jw video player   *}
<div class="unit">
	<h2>{$trans.config_video}</h2>

{if $jw_player_installed == false}
	<p class="message">{$trans.no_jw_message}</p>
{else}

<table>
<tr>
<td colspan="3"><p id="jw_player">There is a problem. I cannot find a player for this file.<p></td></tr>
<tr><td class="left">Choose file type to play</td>
<td colspan="2">
<input type="radio" name="filetoplay" id="play_audio" value="audio" onchange="showJwPlayer();" />Audio file&nbsp;&nbsp;
<input type="radio" name="filetoplay" id="play_video" value="video" checked="checked" onchange="showJwPlayer();" />Video file&nbsp;&nbsp;
<input type="radio" name="filetoplay" id="play_playlist" value="playlist" onchange="showJwPlayer();" />Playlist
</td></tr>
<tr><td class="left">{$trans.jw_width}</td>
<td><input class="narrow" name="jw_video_width" value="{$players.jw_video_width}" id="jw_video_width" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left">{$trans.jw_height}</td>
<td><input class="narrow" name="jw_video_height" value="{$players.jw_video_height}" id="jw_video_height" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left message">{$trans.click}</td><td></td><td></td></tr>
<tr><td class="left">{$trans.jw_backcolor}</td>
<td><input class="color {ldelim}pickerPosition:'right'{rdelim}" name="jw_backcolor" value="{$players.jw_backcolor}" id="jw_backcolor" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left">{$trans.jw_frontcolor}</td>
<td><input class="color {ldelim}pickerPosition:'right'{rdelim}" name="jw_frontcolor" value="{$players.jw_frontcolor}" id="jw_frontcolor" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left">{$trans.jw_lightcolor}</td>
<td><input class="color {ldelim}pickerPosition:'right'{rdelim}" name="jw_lightcolor" value="{$players.jw_lightcolor}" id="jw_lightcolor" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left">{$trans.jw_screencolor}</td>
<td><input class="color {ldelim}pickerPosition:'right'{rdelim}" name="jw_screencolor" value="{$players.jw_screencolor}" id="jw_screencolor" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left">{$trans.jw_controlbar}</td>
<td><select name="jw_controlbar" id="jw_controlbar" onchange="showJwPlayer();">
	<option value="bottom" {if $players.jw_controlbar == 'bottom'}selected="selected"{/if}>{$trans.bottom}</option>
	<option value="over" {if $players.jw_controlbar == 'over'}selected="selected"{/if}>{$trans.over}</option>
	<option value="none" {if $players.jw_controlbar == 'none'}selected="selected"{/if}>{$trans.none}</option>
</select>
</td><td></td></tr>
<tr><td class="left">{$trans.jw_playlist}</td>
<td><select name="jw_playlist" id="jw_playlist" onchange="showJwPlayer();">
	<option value="bottom" {if $players.jw_playlist == 'bottom'}selected="selected"{/if}>{$trans.bottom}</option>
	<option value="over" {if $players.jw_playlist == 'over'}selected="selected"{/if}>{$trans.over}</option>
	<option value="right" {if $players.jw_playlist == 'right'}selected="selected"{/if}>{$trans.right}</option>
	<option value="none" {if $players.jw_playlist == 'none'}selected="selected"{/if}>{$trans.none}</option>
</select>
<tr><td class="left">{$trans.jw_playlistsize}</td>
<td><input class="narrow" name="jw_playlistsize" value="{$players.jw_playlistsize}" id="jw_playlistsize" onchange="showJwPlayer();" /></td><td></td></tr>
<tr><td class="left">{$trans.jw_skin}</td>
<td>
<select name="jw_skin" id="jw_skin" onchange="showJwPlayer();">
	<option value="default" {if $players.jw_skin == 'default'}selected="selected"{/if}>{$trans.default}</option>
	{foreach from=$jw_skins item=skin}
	<option value="{$skin}" {if $players.jw_skin == $skin}selected="selected"{/if}>{$skin}</option>
	{/foreach}
	</select>
</td></tr>
<tr><td class="left">Use default colours for this skin?</td>
<td><input class="radio" name="jw_use_skin_colours" type="radio" value="1" onchange="showJwPlayer();" {if $players.jw_use_skin_colours == "1"}checked="checked"{/if} /> Yes.
<input class="radio" name="jw_use_skin_colours" type="radio" value="0" onchange="showJwPlayer();" {if $players.jw_use_skin_colours == "0"}checked="checked"{/if} /> No.
</td><td></td></tr>

</table>
<br />
<div id="minor">
<h4>{$trans.other_settings}</h4>
<p class="message">{$trans.other_settings_message}</p>
<table>
<tr><td class="left">{$trans.resizing}</td>
<td><select name="jw_resizing" id="jw_resizing" onchange="showJwPlayer();">
	<option value="true" {if $players.jw_resizing == 'true'}selected="selected"{/if}>{$trans.true}</option>
	<option value="false" {if $players.jw_resizing == 'false'}selected="selected"{/if}>{$trans.false}</option>
	</select>
</td><td></td></tr>
<tr><td class="left">{$trans.stretching}</td>
<td><select name="jw_stretching" id="jw_stretching" onchange="showJwPlayer();">
	<option value="none" {if $players.jw_stretching == 'none'}selected="selected"{/if}>{$trans.none}</option>
	<option value="exactfit" {if $players.jw_stretching == 'exactfit'}selected="selected"{/if}>{$trans.exact}</option>
	<option value="uniform" {if $players.jw_stretching == 'uniform'}selected="selected"{/if}>{$trans.uniform}</option>
	<option value="fill" {if $players.jw_stretching == 'fill'}selected="selected"{/if}>{$trans.fill}</option>
</select>
</td><td></td></tr>
<tr><td class="left">{$trans.icons}</td>
<td><select name="jw_icons" id="jw_icons" onchange="showJwPlayer();">
	<option value="1" {if $players.jw_icons == '1'}selected="selected"{/if}>{$trans.show}</option>
	<option value="0" {if $players.jw_icons == '0'}selected="selected"{/if}>{$trans.hide}</option>
</select>
</td><td></td></tr>
</table>
</div>
{/if}
</div> {*  close 'unit'  *}

{*   submit button   *}
<table>
<tr><td class="left"></td><td><input type="submit" value="{$trans.saveset}" class="bottom saveall" /></td><td></td></tr>
</table>

</form>


{/if}  {* close 'admin only' condition  *}


</div> <!--   close content   -->
{include file='manager_footer.tpl'}
