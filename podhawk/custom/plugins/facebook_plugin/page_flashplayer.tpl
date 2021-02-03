{*  template for player for Facebook iframs - we use OnePixelOut player for mp3 files and JW Player for video files  *}

{if $posting.audio_type == 1}

<p id="audioplayer_{$smarty.foreach.postings_loop.iteration}">To use the audio player, your browser needs a Flash plugin and javascript</p>  
	<script type="text/javascript">  
        AudioPlayer.embed("audioplayer_{$smarty.foreach.postings_loop.iteration}", {ldelim}soundFile: "{$posting.audiourl}"{rdelim});  
	</script>

{elseif $posting.playertype == 'jwvideo'}

	<div id="player_{$smarty.foreach.postings_loop.iteration}">Sorry, I cannot find a player to play this file. But you can download it to your computer using the link below.</div>
{if $jw_player_js_embed == true}
	<script type="text/javascript">
	jwplayer("player_{$smarty.foreach.postings_loop.iteration}").setup ({ldelim}
		height : {$posting.jw_vars.height},
		width : {$posting.jw_vars.width},
		id : "player_{$smarty.foreach.postings_loop.iteration}",
		{foreach from=$posting.jw_vars.flashvars key=key item=value name=jw_vars_loop}
		'{$key}' : '{$value}',
		{/foreach}
		modes : [
			{ldelim} type: 'flash', src: '../../players/jwplayer/player.swf' {rdelim},
			{ldelim} type: 'html5' {rdelim}
			]	
		{rdelim});			
			
	</script>
<p><a href="{$posting.web_link}">Download {$posting.mediatypename}</a></p>
{/if}

{/if}
