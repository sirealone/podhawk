<?php

// add setting for New Feed URL
	$dosql = "INSERT INTO " . DB_PREFIX . "lb_settings (name, value) VALUES (:name, :value)";
	$GLOBALS['lbdata']->prepareStatement ($dosql);
	$GLOBALS['lbdata']->executePreparedStatement(array(':name' => 'newFeedURL', ':value' => ''));

?>
