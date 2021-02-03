<?php

//change or translate the terms on the right hand side of the => symbol
//note - comma at end of each line of the array EXCEPT the last

//your e-mail address, as it will appear on the site (encoded to make life difficult for spam robots).
//by default, PodHawk will use the iTunes e-mail address which you entered in the backend settings page.
//if you want a different address, comment out the first line below, uncomment the second line and 
//insert your address where shown.

$my_email = (!veryempty($reg->findSetting('itunes_email'))) ? $reg->findSetting('itunes_email') : "dummy address";
//$my_email = "myname@myemailaddress.com";

//by default, emails will use the name of your site as the subject line. If you want a different subject line,
//comment out the first line below, uncomment the second line below and add the details you want.

$my_subject = SITENAME;
//$my_subject = "SUBJECT";

//some themes give you space to insert information about yourself or the site (and you can add this
//information to other themes if you wish). By default, PodHawk will use the site description information from
//the settings page. If you want to use a different description, comment out the line below,
//uncomment the second line below and insert the information that you want.

$my_info = $reg->findSetting('description');
//$my_info = "PUT WHATEVER YOU WANT HERE";

$trans = (array(
		//translations for navigation
		'next' => 'N&auml;chstes',
		'previous' => 'Vorherige',
		'next_page' => 'N&auml;chste Seite',
		'previous_page' => 'Vorherige Seite',
		'home' => 'Home',
		'home_page' => 'Home page',
		'home_return' => 'Zur&uuml;ck zur Homepage',
		'pages' => 'Seiten',
		'earlier' => 'Fr&uuml;here Posts',
		'later' => 'Sp&auml;tere Posts',
		//translations for postings
		'link_posting' => 'Links zu diesem Post',
		'posted_in' => 'Gepostet in', //followed by list of categories
		'categories' => 'Kategorien',
		'posted_in' => 'Gepostet in',
		'link_categories' => 'Alle Posts in dieser Kategorie',
		'tags' => 'Tags',
		'link_tags' => 'Alle Posts mit Tags',
		'comments' => 'Kommentare',
		'link_comments' => 'Links zu Kommentaren',
		'posted_by' => 'Gepostet von', //author
		'by' => 'von', //author
		'downloaded' => 'heruntergeladen',
		'this_file' => 'Diese Datei wurde',
		'times' => 'Male',
		'no_posts' => 'Verzeihung, ich kann kein Post finden, dass den Suchkriterien entspricht',
	
		//following line will display if user s browser cannot display flash or JW player
		'flash' => 'Es wird ein Flashplayer 9.0.124 oder sp&auml;ter ben&ouml;tigt, um das Dokument korrekt darzustellen', 
		'get' => 'holen',
		'download' => 'herunterladen',
		//translations for comments form
		'says' => 'sagte',
		'says_on' => 'sagte am',  //followed by a date
		'download_comment' => 'Kommentar herunterladen',
		'no_comments' => 'Noch keine Kommentare vorhanden',
		'your_comment' => 'Ihr Kommentar',
		'name' => 'Name',
		'email' => 'E-mail Adresse (wird nicht auf der Website angezeigt)',
		'email_address' => 'E-mail Adresse',
		'email_message' => 'Die E-Mailadresse wird nicht auf der Website angezeigt',
		'your_url' => 'Ihr Hyperlink',
		'spam_message' => 'Um wachsenen Spam-attacken entgegenzuwirken, beantworten sie bitte folgende Frage:',
		'your_message' => 'Ihre Nachricht',
		'upload_audio_message' => 'Sie k&ouml;nnen ein Mp3-Audio-Dokument hochladen,(maximalen Gr&ouml;&szlig;e',
		'no_audio' => 'Kein-Audio-Kommentar zugelassen',
		'preview' => 'Vorschau',
		'send' => 'Kommentar absenden',
		// for disqus comments
		'view_thread' => 'Diskussion ansehen',
		'blog_comments' => 'Blog Kommentare',
		'disqus_powered' => 'Mit Unterst&uuml;tzung von disqus',
		//translations for sidebars
		'nav' => 'Navigation',
		'calendar' => 'Kalender',
		'welcome' => 'Willkommen',
		'this_site_only' => 'Nur diese Website durchsuchen',
		'visitors_last_hour' => 'Besucher der letzten Stunde',
		'visitors_in_last' => 'Besucher in den letzten',
		'minutes' => 'Minuten',
		'archive' => 'Archiv',
		// these are the abbreviated names of the days of the week for the calendar
		'days_string' => 'Mo,Di,Mi,Do,Fr,Sa,So',		
		'about_me' => '&Uuml;ber mich',
		'about_site' => '&Uuml;ber diese Site',
		'subscribe' => 'Abonnieren',
		'rss' => 'RSS-Feed',
		'podcast_feed' => 'Podcast-Feed',
		'rss_comments' => 'RSS Feed mit Kommentaren',
		'podcast_feed_comments' => 'Podcast Feed + Kommentare',
		'rss_link' => 'Link zum RSS-Feed',
		'rss_comments_link' => 'Link zum RSS Feed mit Kommentaren',
		'recent_postings' => 'Aktuelle Posts',
		'recent_comments' => 'Aktuelle Kommentare',
		'last_5_comments' => 'Die letzten 5 Kommentare',
		'comment_link' => 'Auf diesen Kommentar verlinken',
		'authors' => 'Autoren',
		'info_about_me' => $my_info,		
		'your_email' => $my_email,
		'email_subject' => $my_subject,
		'send_me_email' => 'Kontakt via E-Mail',
		'search' => 'Suche',
		'google_search' => 'Google Suche',
		'this_site_search' => 'Diese Website durchsuchen',
		'about' => '&Uuml;ber '.SITENAME,
		// error messages for comments section
		'commentname_too_big' => 'Der von Ihnen genutzte Name ist zu lang (max. 30 Zeichen).',
        'commentmail_too_big' => 'Die von Ihnen eingesetzte eMail-Adresse ist zu lang (max. 30 Zeichen).',
        'commentweb_too_big' => 'Die von Ihnen eingegebene URL ist zu lang (max. 30 Zeichen).',
        'commentmessage_too_big' => 'Ihr Kommentar ist zu lang (max. 1000 Zeichen).',
        'invalid_email' => 'Die von Ihnen eingegebene eMail-Adresse scheint ung&uuml;ltig zu sein.',
        'invalid_url' => 'Die von Ihnen eingegebene URL scheint ung&uuml;ltig zu sein.',
        'message_missing' => 'Bitte hinterlassen Sie eine Nachricht/einen Kommentar.',
        'incorrect_antispam' => 'M&ouml;gliche Spam-Attacke! Sie haben die Anti-Spam-Frage nicht korrekt beantwortet.',
        'missing_antispam' => 'M&ouml;gliche Spam-Attacke! Sie haben die Anti-Spam-Frage nicht beantwortet.',
        'spam' => 'Verzeihen Sie bitte, aber Ihr Kommentar muss leider als Spam gewertet werden.',

		// this is an array of names of the months, used by the calendar.		
		'monthNames' => array(
			'01' => array('long' => 'Januar',
							'short' => 'Jan'),

			'02' => array('long' => 'Februar',
							'short' => 'Feb'),

			'03' => array('long' => 'März',
							'short' => 'Mär'),

			'04' => array('long' => 'April',
							'short' => 'Apr'),

			'05' => array('long' => 'Mai',
							'short' => 'Mai'),

			'06' => array('long' => 'Juni',
							'short' => 'Jun'),

			'07' => array('long' => 'Juli',
							'short' => 'Jul'),

			'08' => array('long' => 'August',
							'short' => 'Aug'),

			'09' => array('long' => 'September',
							'short' => 'Sep'),

			'10' => array('long' => 'Oktober',
							'short' => 'Okt'),

			'11' => array('long' => 'November',
							'short' => 'Nov'),

			'12' => array('long' => 'Dezember',
							'short' => 'Dez')
			)		
));

?>
