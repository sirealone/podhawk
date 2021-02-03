<?php

// URLs may contain only ASCII (ie, broadly, English) letters, numbers, and a few other characters. Everything else has to be coded in the
// form '%xx' (eg '%E4' = 'ä', the German umlauted 'a'). URL encoding is fine for browsers, but it is not readily readable by humans.
// The url_rewrite plugin therefore converts non-ASCII characters in posting titles to similar ASCII charcters before putting the title
// into a URL. In the array below, the charcter on the left of => is the character to be converted; the charcter on the right
// is what it is converted into. Please amend or add to this array to meet the needs of your language. 
// Remember, each element in the array except the last should be followed by a comma.

$replace = array(
	" " => "-",			'ä' => 'ae',		'Ä' => 'Ae',		'ö' => 'oe',		'Ö' => 'Oe',		'ü' => 'ue',		'Ü' => 'Ue',
	'ß' => 'ss',		'á' => 'a', 		'à' => 'a',			'é' => 'e',			'í' => 'i',			'ñ' => 'n',			'ó' => 'o',
	'å' => 'aa',		'æ' => 'ae',		'â' => 'a',			'À' => 'A',			'Á' => 'A',			'Â' => 'A',			'Ã' => 'A',
	'Å' => 'Aa',		'Æ' => 'Ae',		'ç' => 'c',			'Ç' => 'C',			'è' => 'e',			'ê' => 'e',			'ë' => 'e',
	'È' => 'E',			'É' => 'E',			'Ê' => 'E',			'Ë' => 'E',			'ì' => 'i',			'î' => 'i',			'ï' => 'i',
	'Ñ' => 'N',			'ò' => 'o',			'ó' => 'o',			'ô' => 'o',			'õ' => 'o',			'ø' => 'oe',		'Ò' => 'O',
	'Ó' => 'O',			'Ô' => 'O',			'Õ' => 'O',			'Ø' => 'Oe',		'ù' => 'u',			'ú' => 'u',			'û' => 'u',
	'Ù' => 'U',			'Ú' => 'U',			'Û' => 'U',			'ý' => 'y',			'Ý' => 'Y',			'ÿ' => 'y'
	);

$keys = array_keys($replace);

$values = array_values($replace);

?>
