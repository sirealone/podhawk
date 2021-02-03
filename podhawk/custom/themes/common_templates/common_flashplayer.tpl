{*  Flashplayer template modified to permit plugins to show non-standard players  *}

<div id="podhawk_player_{$key}">

{if !empty($posting.plugin_player)}

	{$posting.plugin_player}

{else}

	{if $posting.playertype == 'flash'}

		{if $players.audio_player_type == 'loudblog'}

<object type="application/x-shockwave-flash" data="{$path_to_template}/emff.swf?src={$posting.audiourl}" width="{$players.loudblog_width}" height="{$players.loudblog_height}">
    <param name="movie" value="{$path_to_template}/emff.swf?src={$posting.audiourl}" />
</object>
<br /><br />
	
		{elseif $players.audio_player_type == 'emff'}

<object type="application/x-shockwave-flash" data="podhawk/custom/players/emff/emff_{$players.emff_player}.swf?src={$posting.audiourl}" width="{$players.emff_width}" height="{$players.emff_height}">
<param name="movie" value="podhawk/custom/players/emff/emff_{$players.emff_player}.swf" />
	{if $players.emff_standard_background == false}
<param name="bgcolor" value="#{$players.emff_background}" />
	{/if}
<param name="FlashVars" value="src={$posting.audiourl}" />
</object>
<br /><br />

		{elseif $players.audio_player_type == 'pixelout'}

	<p id="audioplayer_{$smarty.foreach.postings_loop.iteration}">Alternative content</p>  
	<script type="text/javascript">  
        AudioPlayer.embed("audioplayer_{$smarty.foreach.postings_loop.iteration}", {ldelim}soundFile: "{$posting.audiourl}"{rdelim});  
	</script>
	<p></p>

		{elseif $players.audio_player_type == 'jwaudioplayer'}
 
	<div id="player_{$smarty.foreach.postings_loop.iteration}">Sorry, I cannot find a player to play this file. But you can download it to your computer using the link below.</div>

	<script type="text/javascript">
jwplayer ("player_{$smarty.foreach.postings_loop.iteration}").setup ({$posting.jw_vars});
</script>
			
		{/if}
   
	{/if} {* close $posting.playertype == 'flash' *}

	{if $posting.playertype == 'qtaudio' || $posting.playertype == 'qtvideo'}

<object CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="{$posting.qtdata.width}" height="{$posting.qtdata.height}" codebase="http://www.apple.com/qtactivex/qtplugin.cab">  
<param name="src" value="{$posting.qtdata.src}" />
<param name="href" value="{$posting.audiourl}" />
<param name="target" value="{$posting.qtdata.target}" />
<param name="autohref" value="false" />
<param name="autoplay" value="false" />
<param name="controller" value="true" />
<embed src="{$posting.qtdata.src}" href="{$posting.audiourl}" autohref="false" width="{$posting.qtdata.width}" height="{$posting.qtdata.height}" controller="true" target="{$posting.qtdata.target}" autoplay="false" pluginspage="http://www.apple.com/de/quicktime/download/" />
</embed>
</object>
	{/if}

	{if $posting.playertype == 'jwvideo'}
	<div id="player_{$smarty.foreach.postings_loop.iteration}">Sorry, I cannot find a player to play this file. But you can download it to your computer using the link below.</div>
	
<script type="text/javascript">
jwplayer ("player_{$smarty.foreach.postings_loop.iteration}").setup ({$posting.jw_vars});
</script>	
			
	{/if}

{/if} {* close empty($posting.plugin_player)  *}

</div>
