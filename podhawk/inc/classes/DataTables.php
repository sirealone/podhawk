<?php

// This class is used to hold look-up tables.
                    
class DataTables {

	public static function html_to_xml ($text) {
	//turns html entities into UTF-8 or XML entities

	$trans_tbl = array (
		"&"=>"&amp;",
		"&#"=>"&#",
		"<"=>"&lt;",
		">"=>"&gt;",
		"'" => "&apos;",
		'"' => "&quot;"
	);

	$htmlentities_utf8 = array(
	'&nbsp;' => "\xc2\xa0",
	'&iexcl;' => "\xc2\xa1",
	'&cent;' => "\xc2\xa2",
	'&pound;' => "\xc2\xa3",
	'&curren;' => "\xc2\xa4",
	'&yen;' => "\xc2\xa5",
	'&brvbar;' => "\xc2\xa6",
	'&sect;' => "\xc2\xa7",
	'&uml;' => "\xc2\xa8",
	'&copy;' => "\xc2\xa9",
	'&ordf;' => "\xc2\xaa",
	'&laquo;' => "\xc2\xab",
	'&not;' => "\xc2\xac",
	'&shy;' => "\xc2\xad",
	'&reg;' => "\xc2\xae",
	'&macr;' => "\xc2\xaf",
	'&deg;' => "\xc2\xb0",
	'&plusmn;' => "\xc2\xb1",
	'&sup2;' => "\xc2\xb2",
	'&sup3;' => "\xc2\xb3",
	'&acute;' => "\xc2\xb4",
	'&micro;' => "\xc2\xb5",
	'&para;' => "\xc2\xb6",
	'&middot;' => "\xc2\xb7",
	'&cedil;' => "\xc2\xb8",
	'&sup1;' => "\xc2\xb9",
	'&ordm;' => "\xc2\xba",
	'&raquo;' => "\xc2\xbb",
	'&frac14;' => "\xc2\xbc",
	'&frac12;' => "\xc2\xbd",
	'&frac34;' => "\xc2\xbe",
	'&iquest;' => "\xc2\xbf",
	'&Agrave;' => "\xc3\x80",
	'&Aacute;' => "\xc3\x81",
	'&Acirc;' => "\xc3\x82",
	'&Atilde;' => "\xc3\x83",
	'&Auml;' => "\xc3\x84",
	'&Aring;' => "\xc3\x85",
	'&AElig;' => "\xc3\x86",
	'&Ccedil;' => "\xc3\x87",
	'&Egrave;' => "\xc3\x88",
	'&Eacute;' => "\xc3\x89",
	'&Ecirc;' => "\xc3\x8a",
	'&Euml;' => "\xc3\x8b",
	'&Igrave;' => "\xc3\x8c",
	'&Iacute;' => "\xc3\x8d",
	'&Icirc;' => "\xc3\x8e",
	'&Iuml;' => "\xc3\x8f",
	'&ETH;' => "\xc3\x90",
	'&Ntilde;' => "\xc3\x91",
	'&Ograve;' => "\xc3\x92",
	'&Oacute;' => "\xc3\x93",
	'&Ocirc;' => "\xc3\x94",
	'&Otilde;' => "\xc3\x95",
	'&Ouml;' => "\xc3\x96",
	'&times;' => "\xc3\x97",
	'&Oslash;' => "\xc3\x98",
	'&Ugrave;' => "\xc3\x99",
	'&Uacute;' => "\xc3\x9a",
	'&Ucirc;' => "\xc3\x9b",
	'&Uuml;' => "\xc3\x9c",
	'&Yacute;' => "\xc3\x9d",
	'&THORN;' => "\xc3\x9e",
	'&szlig;' => "\xc3\x9f",
	'&agrave;' => "\xc3\xa0",
	'&aacute;' => "\xc3\xa1",
	'&acirc;' => "\xc3\xa2",
	'&atilde;' => "\xc3\xa3",
	'&auml;' => "\xc3\xa4",
	'&aring;' => "\xc3\xa5",
	'&aelig;' => "\xc3\xa6",
	'&ccedil;' => "\xc3\xa7",
	'&egrave;' => "\xc3\xa8",
	'&eacute;' => "\xc3\xa9",
	'&ecirc;' => "\xc3\xaa",
	'&euml;' => "\xc3\xab",
	'&igrave;' => "\xc3\xac",
	'&iacute;' => "\xc3\xad",
	'&icirc;' => "\xc3\xae",
	'&iuml;' => "\xc3\xaf",
	'&eth;' => "\xc3\xb0",
	'&ntilde;' => "\xc3\xb1",
	'&ograve;' => "\xc3\xb2",
	'&oacute;' => "\xc3\xb3",
	'&ocirc;' => "\xc3\xb4",
	'&otilde;' => "\xc3\xb5",
	'&ouml;' => "\xc3\xb6",
	'&divide;' => "\xc3\xb7",
	'&oslash;' => "\xc3\xb8",
	'&ugrave;' => "\xc3\xb9",
	'&uacute;' => "\xc3\xba",
	'&ucirc;' => "\xc3\xbb",
	'&uuml;' => "\xc3\xbc",
	'&yacute;' => "\xc3\xbd",
	'&thorn;' => "\xc3\xbe",
	'&yuml;' => "\xc3\xbf",
	'&fnof;' => "\xc6\x92",
	'&Alpha;' => "\xce\x91",
	'&Beta;' => "\xce\x92",
	'&Gamma;' => "\xce\x93",
	'&Delta;' => "\xce\x94",
	'&Epsilon;' => "\xce\x95",
	'&Zeta;' => "\xce\x96",
	'&Eta;' => "\xce\x97",
	'&Theta;' => "\xce\x98",
	'&Iota;' => "\xce\x99",
	'&Kappa;' => "\xce\x9a",
	'&Lambda;' => "\xce\x9b",
	'&Mu;' => "\xce\x9c",
	'&Nu;' => "\xce\x9d",
	'&Xi;' => "\xce\x9e",
	'&Omicron;' => "\xce\x9f",
	'&Pi;' => "\xce\xa0",
	'&Rho;' => "\xce\xa1",
	'&Sigma;' => "\xce\xa3",
	'&Tau;' => "\xce\xa4",
	'&Upsilon;' => "\xce\xa5",
	'&Phi;' => "\xce\xa6",
	'&Chi;' => "\xce\xa7",
	'&Psi;' => "\xce\xa8",
	'&Omega;' => "\xce\xa9",
	'&alpha;' => "\xce\xb1",
	'&beta;' => "\xce\xb2",
	'&gamma;' => "\xce\xb3",
	'&delta;' => "\xce\xb4",
	'&epsilon;' => "\xce\xb5",
	'&zeta;' => "\xce\xb6",
	'&eta;' => "\xce\xb7",
	'&theta;' => "\xce\xb8",
	'&iota;' => "\xce\xb9",
	'&kappa;' => "\xce\xba",
	'&lambda;' => "\xce\xbb",
	'&mu;' => "\xce\xbc",
	'&nu;' => "\xce\xbd",
	'&xi;' => "\xce\xbe",
	'&omicron;' => "\xce\xbf",
	'&pi;' => "\xcf\x80",
	'&rho;' => "\xcf\x81",
	'&sigmaf;' => "\xcf\x82",
	'&sigma;' => "\xcf\x83",
	'&tau;' => "\xcf\x84",
	'&upsilon;' => "\xcf\x85",
	'&phi;' => "\xcf\x86",
	'&chi;' => "\xcf\x87",
	'&psi;' => "\xcf\x88",
	'&omega;' => "\xcf\x89",
	'&thetasym;' => "\xcf\x91",
	'&upsih;' => "\xcf\x92",
	'&piv;' => "\xcf\x96",
	'&bull;' => "\xe2\x80\xa2",
	'&hellip;' => "\xe2\x80\xa6",
	'&prime;' => "\xe2\x80\xb2",
	'&Prime;' => "\xe2\x80\xb3",
	'&oline;' => "\xe2\x80\xbe",
	'&frasl;' => "\xe2\x81\x84",
	'&weierp;' => "\xe2\x84\x98",
	'&image;' => "\xe2\x84\x91",
	'&real;' => "\xe2\x84\x9c",
	'&trade;' => "\xe2\x84\xa2",
	'&alefsym;' => "\xe2\x84\xb5",
	'&larr;' => "\xe2\x86\x90",
	'&uarr;' => "\xe2\x86\x91",
	'&rarr;' => "\xe2\x86\x92",
	'&darr;' => "\xe2\x86\x93",
	'&harr;' => "\xe2\x86\x94",
	'&crarr;' => "\xe2\x86\xb5",
	'&lArr;' => "\xe2\x87\x90",
	'&uArr;' => "\xe2\x87\x91",
	'&rArr;' => "\xe2\x87\x92",
	'&dArr;' => "\xe2\x87\x93",
	'&hArr;' => "\xe2\x87\x94",
	'&forall;' => "\xe2\x88\x80",
	'&part;' => "\xe2\x88\x82",
	'&exist;' => "\xe2\x88\x83",
	'&empty;' => "\xe2\x88\x85",
	'&nabla;' => "\xe2\x88\x87",
	'&isin;' => "\xe2\x88\x88",
	'&notin;' => "\xe2\x88\x89",
	'&ni;' => "\xe2\x88\x8b",
	'&prod;' => "\xe2\x88\x8f",
	'&sum;' => "\xe2\x88\x91",
	'&minus;' => "\xe2\x88\x92",
	'&lowast;' => "\xe2\x88\x97",
	'&radic;' => "\xe2\x88\x9a",
	'&prop;' => "\xe2\x88\x9d",
	'&infin;' => "\xe2\x88\x9e",
	'&ang;' => "\xe2\x88\xa0",
	'&and;' => "\xe2\x88\xa7",
	'&or;' => "\xe2\x88\xa8",
	'&cap;' => "\xe2\x88\xa9",
	'&cup;' => "\xe2\x88\xaa",
	'&int;' => "\xe2\x88\xab",
	'&there4;' => "\xe2\x88\xb4",
	'&sim;' => "\xe2\x88\xbc",
	'&cong;' => "\xe2\x89\x85",
	'&asymp;' => "\xe2\x89\x88",
	'&ne;' => "\xe2\x89\xa0",
	'&equiv;' => "\xe2\x89\xa1",
	'&le;' => "\xe2\x89\xa4",
	'&ge;' => "\xe2\x89\xa5",
	'&sub;' => "\xe2\x8a\x82",
	'&sup;' => "\xe2\x8a\x83",
	'&nsub;' => "\xe2\x8a\x84",
	'&sube;' => "\xe2\x8a\x86",
	'&supe;' => "\xe2\x8a\x87",
	'&oplus;' => "\xe2\x8a\x95",
	'&otimes;' => "\xe2\x8a\x97",
	'&perp;' => "\xe2\x8a\xa5",
	'&sdot;' => "\xe2\x8b\x85",
	'&lceil;' => "\xe2\x8c\x88",
	'&rceil;' => "\xe2\x8c\x89",
	'&lfloor;' => "\xe2\x8c\x8a",
	'&rfloor;' => "\xe2\x8c\x8b",
	'&lang;' => "\xe2\x8c\xa9",
	'&rang;' => "\xe2\x8c\xaa",
	'&loz;' => "\xe2\x97\x8a",
	'&spades;' => "\xe2\x99\xa0",
	'&clubs;' => "\xe2\x99\xa3",
	'&hearts;' => "\xe2\x99\xa5",
	'&diams;' => "\xe2\x99\xa6",
	'&quot;' => "\x22",
	'&amp;' => "\x26",
	'&lt;' => "\x3c",
	'&gt;' => "\x3e",
	'&OElig;' => "\xc5\x92",
	'&oelig;' => "\xc5\x93",
	'&Scaron;' => "\xc5\xa0",
	'&scaron;' => "\xc5\xa1",
	'&Yuml;' => "\xc5\xb8",
	'&circ;' => "\xcb\x86",
	'&tilde;' => "\xcb\x9c",
	'&ensp;' => "\xe2\x80\x82",
	'&emsp;' => "\xe2\x80\x83",
	'&thinsp;' => "\xe2\x80\x89",
	'&zwnj;' => "\xe2\x80\x8c",
	'&zwj;' => "\xe2\x80\x8d",
	'&lrm;' => "\xe2\x80\x8e",
	'&rlm;' => "\xe2\x80\x8f",
	'&ndash;' => "\xe2\x80\x93",
	'&mdash;' => "\xe2\x80\x94",
	'&lsquo;' => "\xe2\x80\x98",
	'&rsquo;' => "\xe2\x80\x99",
	'&sbquo;' => "\xe2\x80\x9a",
	'&ldquo;' => "\xe2\x80\x9c",
	'&rdquo;' => "\xe2\x80\x9d",
	'&bdquo;' => "\xe2\x80\x9e",
	'&dagger;' => "\xe2\x80\xa0",
	'&Dagger;' => "\xe2\x80\xa1",
	'&permil;' => "\xe2\x80\xb0",
	'&lsaquo;' => "\xe2\x80\xb9",
	'&rsaquo;' => "\xe2\x80\xba",
	'&euro;' => "\xe2\x82\xac");

	$text = trim(strtr($text, array_map('utf8_encode', array_flip(get_html_translation_table(HTML_ENTITIES)))));
	$text = strtr($text, $htmlentities_utf8);
	$text = strtr($text, $trans_tbl);
	return $text;
	}

	public static function itunescats()
	{
		$array = array_flip(array(
		""=>"00-00",
		"Arts"=>"01-00",
			"Design"=>"01-01",
			"Fashion &amp; Beauty"=>"01-02",
			"Food"=>"01-03",
			"Literature"=>"01-04",
			"Performing Arts"=>"01-05",
			"Visual Arts"=>"01-06",
		"Business"=>"02-00",
			"Business News"=>"02-01",
			"Careers"=>"02-02",
			"Investing"=>"02-03",
			"Management &amp; Marketing"=>"02-04",
			"Shopping"=>"02-05",
		"Comedy"=>"03-00",
		"Education"=>"04-00",
			"Education Technology"=>"04-01",
			"Higher Education"=>"04-02",
			"K-12"=>"04-03",
			"Language Courses"=>"04-04",
			"Training"=>"04-05",
		"Games &amp; Hobbies"=>"05-00",
			"Automotive"=>"05-01",
			"Aviation"=>"05-02",
			"Hobbies"=>"05-03",
			"Other Games"=>"05-04",
			"Video Games"=>"05-05",
		"Government &amp; Organizations"=>"06-00",
			"Local"=>"06-01",
			"National"=>"06-02",
			"Non-Profit"=>"06-03",
			"Regional"=>"06-04",
		"Health"=>"07-00",
			"Alternative Health"=>"07-01",
			"Fitness &amp; Nutrition"=>"07-02",
			"Self-Help"=>"07-03",
			"Sexuality"=>"07-04",
		"Kids &amp; Family"=>"08-00",
		"Music"=>"09-00",
		"News &amp; Politics"=>"10-00",
		"Religion &amp; Spirituality"=>"11-00",
			"Buddhism"=>"11-01",
			"Christianity"=>"11-02",
			"Hinduism"=>"11-03",
			"Islam"=>"11-04",
			"Judaism"=>"11-05",
			"Other"=>"11-06",
			"Spirituality"=>"11-07",
		"Science &amp; Medicine"=>"12-00",
			"Medicine"=>"12-01",
			"Natural Sciences"=>"12-02",
			"Social Sciences"=>"12-03",
		"Society &amp; Culture"=>"13-00",
			"History"=>"13-01",
			"Personal Journals"=>"13-02",
			"Philosophy"=>"13-03",
			"Places &amp; Travel"=>"13-04",
		"Sports &amp; Recreation"=>"14-00",
			"Amateur"=>"14-01",
			"College &amp; High School"=>"14-02",
			"Outdoor"=>"14-03",
			"Professional"=>"14-04",
		"Technology"=>"15-00",
			"Gadgets"=>"15-01",
			"Tech News"=>"15-02",
			"Podcasting"=>"15-03",
			"Software How-To"=>"15-04",
		"TV &amp; Film"=>"16-00"
		));

		return $array;
	}

	public static function AudioTypeData ($audio_type=NULL)
	
	{
	// mime - the correct mime type for the media type
	// display - how this media type is described on the webpage eg MP3, Ogg Vorbis
	// provider - used by the JW player to distinguish different file types
	// player - if PodHawk is able to play this file type on the web page, this is the player type (flash, JW Video player) that it uses.
	// tags - MP3 only - the type of ID3 tags which PodHawk writes for this media type
	// rename - true/false - should PodHawk rename this media type on upload to the audio folder (User editable)
	// download_link - should PodHawk display a "Get [this media]" link for this media type (User editable)
	// countdownloads - should Podhawk attempt to count downloads for this file type? And what method should it use? ('getdir', 'htaccess', false)
	// addfiles - should PodHawk permit additional files to be added to a posting where this is the main file type? (true, false)
	// html5 - whether this file type can be played natively in HTML5 on at least some modern browsers (true/false)

		$data = array(

			// mp3
			'1' => array(	'mime' 				=> 'audio/mpeg',
							'display' 			=> 'MP3',		
							'provider'			=> 'sound',
							'player'			=> 'flash',
							'tags'				=> 'id3v2.3',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads' 	=> 'getdir',
							'addfiles' 			=> true,
							'html5'				=> true
						),

			// aac
			'2' => array(	'mime' 				=> 'audio/aac',
							'display' 			=> 'AAC',
							'provider'			=> 'video',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> true
						),

			// ogg
			'3' => array(	'mime' 				=> 'audio/ogg',
							'display' 			=> 'Ogg Vorbis',
							'player'			=> 'jwvideo',
							'tags'				=> 'vorbiscomment',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> true
						),
			
			// wma
			'4' => array(	'mime' 				=> 'application/octet-stream',
							'display' 			=> 'Windows Media',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// wav
			'5' => array(	'mime' 				=> 'audio/vnd.wave',
							'display' 			=> 'WAV',
							'player' 			=> 'qtaudio',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// aif, aiff
			'6' => array(	'mime' 				=> 'audio/aiff',
							'display' 			=> 'AIFF',
							'player' 			=> 'qtaudio',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// mov
			'7' => array(	'mime' 				=> 'video/quicktime',
							'display' 			=> 'QuickTime',
							'player'			=> 'qtvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// avi
			'8' => array(	'mime' 				=> 'video/avi',
							'display' 			=> 'AVI',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// m4b which are not enhanced podcasts
			'9' => array(	'mime' 				=> 'application/octet-stream',
							'display' 			=> 'Audiobook',
							'player' 			=> 'qtaudio',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// mp4 (NB JW Player will play sound only unless video file is H264 encoded)
			'10' => array(	'mime' 				=> 'video/mp4',
							'display' 			=> 'MPEG-4 Video',
							'provider'			=> 'video',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> true
						),

			// wmf
			'11' => array(	'mime' 				=> 'image/x-wmf',
							'display' 			=> 'Windows Media',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// mid, midi
			'12' => array(	'mime' 				=> 'audio/x-midi',
							'display' 			=> 'MIDI',
							'player' 			=> 'qtaudio',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// mpg
			'13' => array(	'mime' 				=> 'video/mpeg',
							'display' 			=> 'MPEG Video',
							'player'			=> 'qtvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// m4a, m4b where a height attribute has been found
			'14' => array(	'mime' 				=> 'application/octet-stream',
							'display' 			=> 'Enhanced Podcast',
							'player' 			=> 'qtaudio',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),
			
			// pdf
			'15' => array(	'mime' 				=> 'application/pdf',
							'display' 			=> 'PDF file',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// ogv
			'16' => array(	'mime' 				=> 'video/ogg',
							'player'			=> 'jwvideo',
							'display' 			=> 'Ogg Theora',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> false
						),

			// m4v, 3pg
			'17' => array(	'mime' 				=> 'VIDEO/MP4',
							'display' 			=> 'MPEG-4 Video',
							'player'			=> 'qtvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// webm
			'18' => array(	'mime' 				=> 'video/webm',
							'display' 			=> 'WebM',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> true
						),

			// XML (but not playlist or slideshow)
			'19' => array(	'mime' 				=> 'text/xml',
							'display' 			=> 'XML File',
							'rename' 			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// flv
			'20' => array(	'mime' 				=> 'video/x-flv',
							'display' 			=> 'Flash Video',
							'provider'			=> 'video',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> false
						),

			// YouTube video
			'21' => array(	'mime' 				=> 'video/x-youtube',
							'display' 			=> 'YouTube Video',
							'provider'			=> 'youtube',
							'player'			=> 'jwvideo',
							'rename'			=> false,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles' 			=> false,
							'html5'				=> true
						),

			// xspf playlist
			'22' => array(	'mime' 				=> 'application/xspf+xml',
							'display' 			=> 'Playlist',
							'player'			=> 'jwvideo',
							'rename'			=> false,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// YouTube playlist
			'23' => array(	'mime' 				=> 'video/x-youtube',
							'display' 			=> 'YouTube Playlist',
							'provider'  		=> 'youtube',
							'player'			=> 'jwvideo',
							'rename'			=> false,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// xml slide show
			'24' => array(	'mime' 				=> 'text/xml',
							'display' 			=> 'Slide Show',
							'player'			=> 'lightbox',
							'rename'			=> true,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// jpg, jpeg
			'25' => array(	'mime' 				=> 'image/jpeg',
							'display' 			=> 'JPG Image',
							'provider'			=> 'image',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// png
			'26' => array(	'mime' 				=> 'image/png',
							'display' 			=> 'PNG Image',
							'provider'			=> 'image',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// gif
			'27' => array(	'mime' 				=> 'image/gif',
							'display' 			=> 'GIF Image',
							'provider'			=> 'image',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link'		=> true,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// ZIP
			'28' => array(	'mime' 				=> 'application/zip',
							'display' 			=> 'ZIP Archive',
							'rename'			=> false,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// GZ
			'29' => array(	'mime' 				=> 'application/gzip',
							'display' 			=> 'GZIP Archive',
							'rename'			=> false,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),

			// rtmp stream with single file
			'30' => array(	'mime' 				=> '',
							'display' 			=> 'Video Stream',
							'provider' 			=> 'rtmp',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// rtmp stream with playlist
			'31' => array(	'mime' 				=> '',
							'display' 			=> 'Video Stream',
							'provider'			=> 'rtmp',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// http stream with single file
			'32' => array(	'mime' 				=> '',
							'display' 			=> 'Video Stream',
							'provider'			=> 'http',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// http stream with playlist
			'33' => array(	'mime' 				=> '',
							'display' 			=> 'Video Stream',
							'provider'			=> 'http',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> false,
							'countdownloads'	=> false,
							'addfiles'			=> false,
							'html5'				=> false
						),

			// m4a
			'34' => array(  'mime' 				=> 'audio/m4a',
							'display' 			=> 'M4A Audio',
							'provider'			=> 'video',
							'player'			=> 'jwvideo',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> true,
							'html5'				=> true
							),

			// wmv
			'35' => array(	'mime' 				=> 'video/x-ms-wmv',
							'display' 			=> 'Windows Media',
							'rename'			=> true,
							'download_link' 	=> true,
							'countdownloads'	=> 'htaccess',
							'addfiles'			=> false,
							'html5'				=> false
						),
				);

		if (empty($audio_type))
		{
			return array('mime' => '', 'display' => '', 'provider' => '', 'player' => '', 'download_link' => false);
		}
		else
		{
			return $data[$audio_type];
		}

	}

	public static function getAudioType($extension)
	// a table which links the file extension with a code representing the type of audio/video file
	{
		$array = array(
			"mp3"	=>	"1",
			"aac"	=>	"2",
			"mp4"	=>	"10",
			"m4v"	=>	"17",
			"3gp"	=>	"17",
			"ogg"	=>	"3",
			"wma"	=>	"4",
			"wmv"	=>	"35",
			"wmf"	=>	"11",
			"wav"	=>	"5",
			"aif"	=>	"6",
			"aiff"	=>	"6",
			"mov"	=>	"7",
			"avi"	=>	"8",
			"mid"	=>	"12",
			"midi"	=>  "12",
			"mpg"	=>	"13",
			"mpe"	=>  "13",
			"mpeg"	=>	"13",
			"pdf"	=>	"15",
			"flv"	=>	"20",
			"xml"	=>	"22",
			"jpg"	=>	"25",
			"jpeg"	=>	"25",
			"png"	=>	"26",
			"gif"	=>	"27",
			"ogv"	=>	"16",
			'm4a'	=>	'34',
			'm4b'	=>	'9',
			'webm'	=>  '18',
			'zip'	=>	'28',
			'gz'	=>	'29'
			);
		if ($extension == 'array_keys')
		{
			return array_keys($array);
		}

		elseif (!isset($array[$extension]))
		{
			return '0';
		}
		else
		{
			return $array[$extension];
		}
	}

	public static function uploadErrors($err)
	// translates the numeric error code returned when a file upload fails into something that makes sense to people	
	{
		switch ($err)
		{
		    case UPLOAD_ERR_INI_SIZE:
		        return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		    case UPLOAD_ERR_FORM_SIZE:
		        return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
		    case UPLOAD_ERR_PARTIAL:
		        return 'The uploaded file was only partially uploaded';
		    case UPLOAD_ERR_NO_FILE:
		        return 'No file was uploaded';
		    case UPLOAD_ERR_NO_TMP_DIR:
		        return 'Missing a temporary folder';
		    case UPLOAD_ERR_CANT_WRITE:
		        return 'Failed to write file to disk';
		    case UPLOAD_ERR_EXTENSION:
		        return 'File upload stopped by extension';
		    default:
		        return 'Unknown upload error';
    	}
	}

		public static function findEditor($id)
		// gets name of Editor from numerical code 
		{
			switch($id)
			{
				case 1 :
				return "Textile";
				break;

				case 2 :
				return "Markdown (deprecated)";
				break;

				case 3 :
				return "BBCode (deprecated)";
				break;

				case 4 :
				return "Tiny MCE";
				break;

				case 5:
				return "Very Simple Editor";
				break;

				case 6:
				return "Raw HTML";
				break;
	
				default :
				return "No editor";
				break;
			}
		}
	}
?>
