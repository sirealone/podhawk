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
		'next' => 'Next',
		'previous' => 'Previous',
		'next_page' => 'Next page',
		'previous_page' => 'Previous page',
		'home' => 'Home',
		'home_page' => 'Home page',
		'home_return' => 'Return to home page',
		'pages' => 'Pages',
		'earlier' => 'Earlier postings',
		'later' => 'Later postings',
		'first' => 'First', // new in PodHawk 1.8
		'last' => 'Last',	// new in PodHawk 1.8
		//translations for postings
		'link_posting' => 'Link to this posting',
		'posted_in' => 'Posted in', //followed by list of categories
		'categories' => 'Categories',
		'posted_in' => 'Posted in',
		'link_categories' => 'All postings in category',
		'tags' => 'Tags',
		'link_tags' => 'All postings tagged',
		'comments' => 'Comments',
		'link_comments' => 'Link to comments',
		'posted_by' => 'Posted by', //author
		'by' => 'by', //author
		'downloaded' => 'This file has been downloaded',
		'times' => 'times',
		'no_posts' => 'Sorry, I cannot find any postings which match your request',
		//following line will display if user's browser cannot display flash or JW player
		'flash' => 'You need Flash player 9.0.124 or later to see the player',
		'get' => 'Get',
		'download' => 'Download',
		//translations for comments form
		'says' => 'says',
		'says_on' => 'says on',  //followed by a date
		'download_comment' => 'Download comment',
		'no_comments' => 'No comments yet',
		'your_comment' => 'Your Comment',
		'name' => 'Name',
		'email' => 'E-mail address (will not appear on the website)',
		'email_address' => 'E-mail address',
		'email_message' => 'Your e-mail address will not appear on the website',
		'your_url' => 'Your URL',
		'spam_message' => 'To prevent automated spam attacks, please answer the following question.',
		'your_message' => 'Your message',
		'upload_audio_message' => 'You can upload an mp3 audio file (maximum size',
		'no_audio' => 'No audio file allowed',
		'preview' => 'preview',
		'send' => 'send comment',
		'comments_closed' => 'Comments on this post are now closed.',
		// for disqus comments
		'view_thread' => 'View discussion thread',
		'blog_comments' => 'blog comments',
		'disqus_powered' => 'powered by disqus',
		//translations for sidebars
		'nav' => 'Navigation',
		'calendar' => 'Calendar',
		'welcome' => 'Welcome',
		'this_site_only' => 'only search this site',
		'visitors_last_hour' => 'Visitors in last hour',
		'visitors_in_last' => 'Visitors in last',
		'minutes' => 'minutes',
		'archive' => 'Archive',
		// these are the abbreviated names of the days of the week for the calendar
		'days_string' => 'M,T,W,Th,F,Sa,Su',		
		'about_me' => 'About me',
		'about_site' => 'About this site',
		'subscribe' => 'Subscribe',
		'rss' => 'RSS Feed',
		'podcast_feed' => 'Podcast feed',
		'rss_comments' => 'RSS Feed with comments',
		'podcast_feed_comments' => 'Podcast feed + comments',
		'rss_link' => 'Link to RSS feed',
		'rss_comments_link' => 'Link to RSS feed with comments',
		'recent_postings' => 'Recent Postings',
		'recent_comments' => 'Recent Comments',
		'last_5_comments' => 'Last 5 Comments',
		'comment_link' => 'Link to this comment',
		'authors' => 'Authors',
		'info_about_me' => $my_info,		
		'your_email' => $my_email,
		'email_subject' => $my_subject,
		'send_me_email' => 'Send me an email',
		'search' => 'Search',
		'google_search' => 'Google search',
		'this_site_search' => 'Search this site',
		'about' => 'About '.SITENAME,

		// error messages for the comment system
		'commentname_too_big' => 'The name which you have entered is too long (max size 30 characters).',
		'commentmail_too_big' => 'The e-mail address which you have entered is too long (max size 30 characters).',
		'commentweb_too_big' => 'The URL which you have entered is too long (max size 30 characters).',
		'commentmessage_too_big' => 'Your comment is too long (max size 1000 characters).',
		'invalid_email' => 'The e-mail address which you have entered is not valid.',
		'invalid_url' => 'The URL which you have entered is not valid.',
		'message_missing' => 'You must write a message in the message box below.',
		'incorrect_antispam' => 'Possible spam attack! You have not answered the anti-spam question correctly.',
		'missing_antispam' => 'Possible spam attack! You have not sent an answer to the anti-spam question.',
		'spam' => 'Sorry! Your comment appears to be spam.',
		
		// this is an array of names of the months, used by the calendar.		
		'monthNames' => array(
			'01' => array('long' => 'January',
							'short' => 'Jan'),

			'02' => array('long' => 'February',
							'short' => 'Feb'),

			'03' => array('long' => 'March',
							'short' => 'Mar'),

			'04' => array('long' => 'April',
							'short' => 'Apr'),

			'05' => array('long' => 'May',
							'short' => 'May'),

			'06' => array('long' => 'June',
							'short' => 'Jun'),

			'07' => array('long' => 'July',
							'short' => 'Jul'),

			'08' => array('long' => 'August',
							'short' => 'Aug'),

			'09' => array('long' => 'September',
							'short' => 'Sep'),

			'10' => array('long' => 'October',
							'short' => 'Oct'),

			'11' => array('long' => 'November',
							'short' => 'Nov'),

			'12' => array('long' => 'December',
							'short' => 'Dec')
			)		
));

	
?>
