<?php

class ogg extends PluginPattern {

function __construct ($data=NULL) {

	$this->myName = "ogg";
	$this->myFullName = "Ogg Support Module";
	$this->version = "1.0";
	$this->description = "This plugin allows Ogg Vorbis (audio) and Ogg Theora (video) files to be played on your PodHawk website, in the same way as mp3 files are played. If the user's browser is Firefox 3.5 or later, or Google Chrome, the Ogg files will play using these browsers' inbuilt support for the HTML5 audio and video tags. In other browsers with Java support, there is an option to play the file using the <a href=\"http://www.theora.org/cortado/\">Cortado</a> Java applet.";
	$this->author = "Peter Carter";
	$this->contact = "cpetercarter@googlemail.com";
	$this->initialParams = array("use_cortado" => 0, "video_width" => 320, "video_height" => 240, "audio_width" => 320, "audio_height" => 20);

	$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
	$this->enabled = (!empty($data['enabled'])) ? $data['enabled'] : 0;

	$this->listeners = array('onPostingDataReady', 'onBackendPostingDataReady', 'onSavePosting');

	}

protected function backendPluginsPage() {

	$check0 = ($this->params['use_cortado'] == 0) ? "checked=\"checked\"" : "";
	$check1 = ($this->params['use_cortado'] == 1) ? "checked=\"checked\"" : "";

	$html = <<<EOF
	<tr>
		<td class="left">Use Cortado player in browsers without HTML5/Ogg support</td>
		<td class="center">
			<input class="radio" name="use_cortado" type="radio" value="0" $check0 />No&nbsp;&nbsp;
			<input class="radio" name="use_cortado" type="radio" value="1" $check1 />Yes</td>
		<td class="right">The Cortado player requires a Java plugin in the user's browser. It sometimes fails to load the audio/video file and may in some circumstances crash the browser. If you use it, monitor feedback from your users about their experience of the player.</td>
	</tr>
	<tr>
		<td class="left">The width of the video player (pixels)</td>
		<td class="center"><input type="text" name="video_width" value="{$this->params['video_width']}" /></td>
		<td class="right"></td>
	</tr>
	<tr>
		<td class="left">The height of the video player (pixels)</td>
		<td class="center"><input type="text" name="video_height" value="{$this->params['video_height']}" /></td>
		<td class="right"></td>
	</tr>
	<tr>
		<td class="left">The width of the audio player (pixels)</td>
		<td class="center"><input type="text" name="audio_width" value="{$this->params['audio_width']}" /></td>
		<td class="right"></td>
	</tr>
	<tr>
		<td class="left">The height of the audio player (pixels)</td>
		<td class="center"><input type="text" name="audio_height" value="{$this->params['audio_height']}" /></td>
		<td class="right"></td>
	</tr>
EOF;
	return $html;

	}

protected function getParamsFromPosts() {


	$params = array();
	$array = array("use_cortado", "video_width", "video_height", "audio_width", "audio_height");
	foreach ($array as $item) {
		$params[$item] = (isset($_POST[$item])) ? $_POST[$item] : "";
		}
	return $params;

	}

public function onPostingDataReady($postings) {

	$return = array();

	foreach ($postings as $key => $posting) {
		$html = "";

		//HTML5 <audio> and <video> will download the file automatically on each page load. If we link to the audio file
		//via the download counting engine, we will get a misleadingly high figure for downloads. So we link directly to the file.

		$audio_url = ($posting['filelocal'] == true) ? THIS_URL . "/audio/" . $posting['audio_file'] : $posting['audio_file'];
		 		
		if ($posting['audio_type'] == "3") {

				$html .= "<audio controls style=\"width:{$this->params['audio_width']}px;\" width=\"{$this->params['audio_width']}\">
			<source src=\"" . $audio_url . "\" />";

		if ($this->params['use_cortado'] == 1) {

		$html .= "<object type=\"application/x-java-applet\" width=\"{$this->params['audio_width']}\" height=\"{$this->params['audio_height']}\">  
     			<param name=\"archive\" value=\"" . THIS_URL . "/podhawk/custom/plugins/{$this->myName}/cortado_latest.jar\" />  
     			<param name=\"code\" value=\"com.fluendo.player.Cortado.class\" />  
    			<param name=\"url\" value=\"" . $posting['audiourl'] . "\" />
			<param name=\"autoplay\" value=false />  
     			<p>You need to install Java to play this file.</p>  
  			</object>";
				}
	
		$html .= "</audio>";

		$return[] = array('plugin' => $this->myName,
				'variable' => 'postings',
				'offset' => array($key, 'plugin_player'),
				"value"=>$html);

		
			}

		if ($posting['audio_type'] == "16")  {

		$html .= "<video controls style=\"width:{$this->params['video_width']}px;\" width=\"{$this->params['video_width']}\" height=\"{$this->params['video_height']}\">
			<source src=\"" . $audio_url . "\" />";

		if ($this->params['use_cortado'] == 1)  {

			$html .= "<object type=\"application/x-java-applet\" width=\"{$this->params['video_width']}\" height=\"{$this->params['video_height']}\">  
     			<param name=\"archive\" value=\"" . THIS_URL . "/podhawk/custom/plugins/{$this->myName}/cortado_latest.jar\" />  
     			<param name=\"code\" value=\"com.fluendo.player.Cortado.class\" />  
    			<param name=\"url\" value=\"" . THIS_URL . "/audio/" . $posting['audio_file'] . "\" />
			<param name=\"autoplay\" value=false />  
     			<p>You need to install Java to play this file.</p>  
  			</object>";

				}
			$html .= "</video>";

		$return[] = array('plugin' => $this->myName,
				'variable' => 'postings',
				'offset' => array($key, 'plugin_player'),
				"value"=>$html);
			}
		}
 
	return $return;

	}

public function onBackendPostingDataReady($posting)  {

		$return = array();

		if ($posting['audio_type'] == 3) {

			$html = "<audio controls style=\"width:250px;\">
				<source src=\"" . $posting['audio_link'] . "\" />
				</audio>";

			$return[] = array('plugin' => $this->myName,
					'variable' => 'fields',
					'offset' => array('plugin_player'),
					'value' => $html);

			$html = "<h3>Ogg Support Module</h3>
			<p>This file is .... <input class=\"radio\" type=\"radio\" name=\"audio_type\" value=\"3\" checked=\"checked\" />Ogg audio&nbsp;&nbsp;
			<input class=\"radio\" type=\"radio\" name=\"audio_type\" value=\"16\" />Ogg video</p><br />";

			$return[] = array('plugin' => $this->myName,
					'variable' => 'fields',
					'offset' => array('rec2_html1'),
					'value' => $html);

			}

		if ($posting['audio_type'] == 16) {
			
				$html = "<video controls style=\"width:250px;\" width=\"250\" height=\"200\">
				<source src=\"" . $posting['audio_link'] . "\" />
				</video>";

			$return[] = array('plugin' => $this->myName,
					'variable' => 'fields',
					'offset' => array('plugin_player'),
					'value' => $html);
			$html = "<h3>Ogg Support Module</h3>
			<p>This file is ....<input class=\"radio\" type=\"radio\" name=\"audio_type\" value=\"3\" />Ogg audio&nbsp;&nbsp;
			<input class=\"radio\" type=\"radio\" name=\"audio_type\" value=\"16\" checked=\"checked\" />Ogg video</p><br />";

			$return[] = array('plugin' => $this->myName,
					'variable' => 'fields',
					'offset' => array('rec2_html1'),
					'value' => $html);
			}

		return $return;

	}

public function onSavePosting($id) {

	if (isset($_POST['audio_type']))  {

	$dosql = "UPDATE ".DB_PREFIX."lb_postings SET audio_type = " . escape($_POST['audio_type']) . " WHERE id = " . escape($id) . ";";
	$GLOBALS['lbdata']->Execute($dosql);

		}
	}
}
?>
