<?php

$trans_mediaelement = array(
	
	'description' => "The MediaElement Video and Audio player is a HTML5 and CSS player, with a Flash fallback for older browsers. This plugin will replace your default player with the MediaElement player when the audio/video file is mp3, ogg audio, aac, mp4, WebM or YouTube video. It requires PodHawk 1.83 or later.",
	'dimensions_help' => 'You can set the dimensions of the MediaElement player, and the player skin and background colour, below.',
	'video_width' => 'Width of the video player',
	'video_height' => 'Height of the video player',
	'video_top_margin' => 'Margin above video player',
	'margin_help' => 'Set the margin so that the player looks right with your theme.',
	'audio_width' => 'Width of the audio player',
	'audio_height' => 'Height of the audio player',
	'audio_top_margin' => 'Margin above audio player',
	'margin_below' => 'Margin below audio/video player',
	'skin' => 'Skin',
	'bgcolour' => 'Background colour',
	'bgcolour_help' => 'Select a background colour for the player as a hex colour value (eg #82A2C2 for a blue colour). Click select box for colour picker. Or enter \'default\', \'transparent\', or \'random\'.', // translators - 'default', 'transparent' and 'random' must be kept as English words
	'preload' => 'Preload the audio/video file?',
	'none' => 'none',
	'metadata' => 'metadata',
	'auto' => 'auto',
	'browser' => 'browser',
	'preload_help' => '"None" = the player will load the file only when the user clicks the play button.<br />"Metadata" = the player will download the first part of the file when the page loads.<br />"Auto" = the player will download the whole file on page load.<br />"Browser" = use the default \'preload\' setting in the user\'s browser (usually \'metadata\').',
	'preload_warning' => 'NOTE : If you choose "metadata" or "auto", the PodHawk download counting system will record a download whenever a user loads the page instead of when the user clicks the play button. These settings may also increase the bandwidth that your server uses. If in doubt, use "none".'
);

?>
