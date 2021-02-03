{*  Flashplayer template for postings page  *}


{if $post.playertype == 'flash'}

<object type="application/x-shockwave-flash" data="custom/players/emff/emff_easy_glaze_small.swf?src={$post.audio_link}" width="22" height="22">
<param name="movie" value="custom/players/emff/emff_easy_glaze_small.swf" />
<param name="bgcolor" value="#F4F4E3" />
<param name="FlashVars" value="src={$post.audio_link}" />
</object>


{else}  {*  show a link only, if the file is not mp3  *}

	<p><a href="{$post.audio_link}">Download</a></p>
{/if}

