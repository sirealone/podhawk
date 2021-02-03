{* iPad and iPhone do not support Flash. So we replace Flash-only players with browser's own HTML5 player where Flash is not installed *}

{* have we chosen a flash-only audio player? *}
{if ($players.audio_player_type == 'loudblog' || $players.audio_player_type == 'emff' || $players.audio_player_type == 'pixelout')}

{* if test whether browser has a Flash plugin *}
		
	<script type="text/javascript">
		var ie_flash; var non_ie_flash;
		try {ldelim} ie_flash = (typeof window.ActiveXObject != 'undefined' && (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) != false);{rdelim}
		catch(err) {ldelim} ie_flash = false;{rdelim}
		try {ldelim} non_ie_flash = (typeof navigator.plugins != "undefined" && typeof navigator.plugins["Shockwave Flash"] == "object");{rdelim}
		catch(err) {ldelim} non_ie_flash = false;{rdelim}
		var flash_available = ie_flash || non_ie_flash;
{* if we want to play an mp3 file, and there is no Flash plugin, use html5 default player *}
		{foreach from=$postingdata key=key item=posting name=ipad_loop}
			{if $posting.playertype == 'flash' && empty($posting.plugin_player)}				
				$(document).ready(function() {ldelim}
				if (!flash_available) {ldelim}
				var html = '<audio controls="controls" preload="none"> <source src="{$posting.audiourl}" type="audio/mpeg">';
				{foreach from=$posting.addfiles item=addfile}
				html += '<source src="{$addfile.audiourl}" type="{$addfile.mime}">';
				{/foreach}
				html += "Sorry, I cannot find an audio player for this file.</audio>";
				$('#podhawk_player_{$key}').empty().append(html);
				{rdelim}
			{rdelim});
			{/if}		
		{/foreach}
	</script>
{/if}		
