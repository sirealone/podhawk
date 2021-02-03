{*  Flashplayer template for Recording page 2   *}

{if !empty($posting.plugin_player)}

	{$posting.plugin_player}

{else}

	{if $posting.playertype == 'flash'}

	<object type="application/x-shockwave-flash" data="custom/players/emff/emff_stuttgart.swf?src={$posting.audio_link}" width="140" height="30">
	<param name="movie" value="custom/players/emff/emff_stuttgart.swf" />
	<param name="bgcolor" value="#F4F4E3" />
	<param name="FlashVars" value="src={$posting.audio_link}" />
	</object>

	{elseif $posting.playertype == 'qtaudio'}

	<object CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="280" height="16" codebase="http://www.apple.com/qtactivex/qtplugin.cab">  
			<param name="src" value="{$url}/podhawk/backend/clicktoplayback2.mov" />
		<param name="href" value="{$posting.audio_link}" />
		    <param name="target" value="myself" />
	   	<param name="autohref" value="false" />
		    <param name="autoplay" value="false" />
		    <param name="controller" value="false" />
			 <embed src="{$url}/podhawk/backend/clicktoplayback2.mov" href="{$posting.audio_link}" autohref="false" width="280" height="16" controller="false" target="myself" autoplay="false" pluginspage="http://www.apple.com/de/quicktime/download/" />
	   	</embed></object>


	{elseif $posting.playertype == 'qtvideo'}
	<object CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="280" height="16" codebase="http://www.apple.com/qtactivex/qtplugin.cab">  
			<param name="src" value="{$url}/podhawk/backend/clicktoplayback2.mov" />
		<param name="href" value="{$posting.audio_link}" />
		    <param name="target" value="quicktimeplayer" />
	   	<param name="autohref" value="false" />
		    <param name="autoplay" value="false" />
		    <param name="controller" value="false" />
			 <embed src="{$url}/podhawk/backend/clicktoplayback2.mov" href="{$posting.audio_link}" autohref="false" width="280" height="16" controller="false" target="quicktimeplayer" autoplay="false" pluginspage="http://www.apple.com/de/quicktime/download/" />
	   	</embed></object>
	{elseif $posting.playertype == 'jwvideo'}

		<div id="player">Sorry - I cannot find a player for this file.</div>
	
		<script type="text/javascript">
		jwplayer("player").setup ({ldelim}
			height : 180,
			width : 260,
			'flashplayer' : 'custom/players/jwplayer/player.swf',
		{if $posting.jw_streamer != ""}
			'file': '{$posting.jw_streaming_file}',
			'streamer': '{$posting.jw_streamer}',
		{else}
			'file': '{$posting.audio_link}',
		{/if}
			'resizing': 'false',
		{if $posting.audio_type == 22 || $posting.audio_type == 23 || $posting.audio_type == 31 || $posting.audio_type == 33}
			'playlist': 'bottom', 'playlistsize': '50',
		{/if}
			'skin': 'custom/players/jwplayer/skins/simple.swf'
			{rdelim});
		</script>
	
	{/if}
{/if}
