<?php

class rainbow_player extends PluginPattern {

function __construct ($data=NULL) {

	$this->myName = "rainbow_player";
	$this->myFullName = "Rainbow Player";
	$this->description = "This plugin sets the One Pixel Out player as the webpage player for mp3 files, and randomises the colours of the player. The player colours will change every time you reload the page (if you have caching switched off) or every hour (if you have caching switched on). Silly, pointless but quite fun!";
	$this->version = "1.0";
	$this->author = "Peter Carter";
	$this->contact = "cpetercarter@googlemail.com";
	
	$this->initial_params = array();

	$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;
$this->enabled = $data['enabled'];

	$this->listeners = array("onAllPageDataReady");

	}

// there are no user defined parameters for this plugin..
protected function backendPluginsPage()  {

	return $this->noUserParams();

	}

//...and therefore no $_POST variables to convert into parameters
protected function getParamsFromPosts() {

	return array();

	}

//when all the (frontend webpage) page data is ready to send to  Smarty...	
public function onAllPageDataReady() {

	$return = false;

	//...we set the One Pixel Out player as the player for mp3s	
	$return[] = array ('plugin' => $this->myName,
				'variable' => 'players',
				'offset' => array('audio_player_type'),
				'value' => 'pixelout'
				);
		
	//..and randomise the main colour attributes for the player
	$player_atts = array("pix_leftbackground", "pix_lefticon", "pix_rightbackground", "pix_righticon", "pix_righticonhover", "pix_rightbackgroundhover", "pix_border", "pix_loader");

	foreach ($player_atts as $att) {

		$return[] = array('plugin' => $this->myName, 'variable' => 'players', 'offset'=>array($att), 'value'=>$this->randomColour());

		}

	// ..and ensure that swf object is loaded

		$return[] = array('plugin' => $this->myName, 'variable' => 'javascript', 'offset' => array("swfobject"), 'value' => "http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js");

	// and that the JS file for the player is loaded
		$return[] = array('plugin' => $this->myName, 'variable' => 'javascript', 'offset' => array('pixelout'), 'value' => 'podhawk/custom/players/onepixelout/audio-player-uncompressed.js');

	//..and that the head section knows that a pixout player is required
		$return[] = array(	'plugin' 	=> $this->myName,
							'variable' 	=> 'pixout_required',
							'offset' 	=> array(),
							'value' 	=> true); 

	return $return;
	}	 

//a function for generating random colours
private function randomColour () {
	$c = '';
	for ($i = 0; $i<6; $i++)
	    {
	     $c .=  dechex(rand(0,15));
	    }
	return $c;
	} 

}
?>
