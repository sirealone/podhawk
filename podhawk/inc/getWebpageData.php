<?php

	$actiontype = array('webpage');
	include 'authority.php';

	$smarty->assign('settings', $reg->getSanitisedSettings());
	$smarty->assign('config_file', $theme.".conf");

	$players = $reg->getPlayers();

	// plugins may want to change player data....
	$h = $plugins->event('onPlayerDataReady');
	if ($h)
	{
		foreach ($h as $j) rewriteVariables($j);
	}	

	// language for 'content-language' metatag
	$webpageLanguage = DEFAULT_WEBPAGE_LANGUAGE;

	## Posting Data ##

	// an empty array into which we will write posting data
	$postings = array();

	// we get the postings data we need
	$pagination = new PO_Pagination_Webpage();
	$rawPostings = $pagination->getRows();

	// if $rawPostings is empty, we need to avoid getting a slew of error messages

	if (count($rawPostings) > 0)
	{

		## Single post actions ##

		if (isset($_GET['id']))
		{
			session_start();

			$postingCommentOn = $rawPostings[$_GET['id']]['comment_on'];

			$comh = new PO_Comment($_GET['id'], $postingCommentOn, $rawPostings[$_GET['id']]['comment_size']);
	
			$smarty->assign('accept_comments', $comh->acceptComments());

			if($comh->showCommentForm() == true)
			{

				$warning_thrown = false;

				if (isset($_POST['commentname']))
				{
					$comh->processNewComment();

					if ($comh->newComment->clearCache() == true)
					{
						$smarty->clear_all_cache();
					}
					
					$warning = $comh->newComment->getWarning();

					$smarty->assign ('comment_warning', $warning);
				}

				//if comments are switched on, assign variables to Smarty needed to display the comments form
	 	 		$smarty->assign(array( 	'comment_data'   		  => $comh->prepareComment(),
										'sendbutton_test'		  => $comh->sendData(),
										'spam_question'  		  => $reg->findSetting('spamquestion'),
										'comment_preview'		  => isset($_POST['commentpreview'])										
										));
			}

		} //end single post actions


		## Modify the data for each posting to make it more useful ##

		foreach ($rawPostings as $posting)
		{
			$postingManager = new PO_Posting_Webpage($posting);			
			
			$postingManager->extendPostingData();

			$key = $postingManager->getID();			

			$postings[$key] = $postingManager->getPosting();	
			
			// for reasons of backwards compatibility with templates
			// links, categories and tags associated with the posting are separated into their own arrays
			$posting_links[$key] 		= $postings[$key]['links'];
			$posting_categories[$key] 	= $postings[$key]['categories'];
			$posting_tags[$key] 		= $postings[$key]['tag_array'];
			$posting_tag_links[$key] 	= $postings[$key]['tag_links'];

			$comments = new PO_Posting_Comments($key);			

			// count the comments
			$posting_comments_count[$key] = $comments->getCount();
			
			//....and get the comments only for a single post page
			$posting_comments[$key] = (isset($_GET['id'])) ? $comments->getComments() : array();			
		
		} //end modifications to posting data

		## onPostingDataReady event  ##

		// send data about postings to Plugins...
		$h = $plugins->event('onPostingDataReady', $postings);

			//...and write any changes
			if ($h)
			{
				foreach ($h as $j) rewriteVariables($j);
			}

		// then get from plugins information about stuff to be added to the end of the posting text
		foreach ($postings as $key => $posting)
		{
			$data = $plugins->makePostingFooter($key);
			if ($data)
			{
				$postings[$key]['message_html'] .= $data;
			}
		}

		## Assign posting data to Smarty ##

		$smarty->assign(array(	'postingdata'				=> $postings,
					  			'posting_links'				=> $posting_links,
								'posting_comments_count' 	=> $posting_comments_count,				 
					 			'posting_comments'			=> $posting_comments,
					  			'posting_categories'		=> $posting_categories,
					  			'posting_tags'				=> $posting_tags,
								'posting_tag_links'			=> $posting_tag_links));


	} // end the 'if count($postings) > 0' condition

	$smarty->assign(array(  'rss_feed' => $reg->getFeedAddress(),
							'rss_comment_feed' => $reg->getCommentFeedAddress()));

	$metaTags = new TR_WebpageMetatags($theme, $plugins);

	$metaTags->setWebpageLanguage($webpageLanguage);

	## Pagination ##
	$nextpage			= $pagination->getNextPage();
	$previouspage		= $pagination->getPreviousPage();
	$nextpageurl		= $pagination->getNextPageURL();
	$previouspageurl	= $pagination->getPreviousPageURL();

	$homepage = false;
	if ((count($_GET)== 0) OR ((isset($_GET['page'])) && ($_GET['page'] == 1) && (count($_GET) == 1)))
	{
		$homepage = true;		
	} 

	## Tags ##
	$tags = TagManager::instance();

	$tag_links 		= $tags->getTaglinks();
	$tag_list 		= $tags->getSortedTagList();
	$tag_weights 	= $tags->getTagCloudWeights();
	
		
	## Categories ##
	$cats = $reg->getCategoriesArray();
	foreach ($cats as $cat)
	{
		// create an associative array of categories and add link
		$categories[$cat['id']] = $cat;
		$categories[$cat['id']]['link'] = 'index.php?cat=' . rawurlencode(my_html_entity_decode($cat['name']));
	}

	## Authors ##
	$authors = $reg->getAuthorslist();

	## One-pixel-out player
	$pixout_required = false;
	if ($players['audio_player_type'] == 'pixelout')
	{
		foreach ($postings as $posting)
		{
			if ($posting['playertype'] == "flash")
			{
				$pixout_required = true;
			}
		}
	}

	## JW Player ##

	//is the JW player installed?
	$jw_player_installed = jwplayer_installed();

	//and do we need to show a jw player on the page?
	$jw_player_required = $pagination->jwPlayerRequired();

	## lightbox ##
	$lightboxRequired = $pagination->lightboxRequired();

	## Languages ##

	$t = new TR_TranslationWebpage($theme);

	$smarty->assign('trans', $t->getTrans());

	## any other page elements? ##

	//an empty array into which plugins can write 'free' webpage elements
	$pluginsPageElements = array();


	## Metatags ##

	$summary = (isset($_GET['id']) && isset($postings[$_GET['id']]['summary'])) ? $postings[$_GET['id']]['summary'] : NULL;
	$metatags = $metaTags->getStandardTags($summary);
	
	## css ##

	$css = $metaTags->getStyleSheets($lightboxRequired);
		
	## namespaces ##

	$namespaces = $metaTags->getNameSpaces();
	
	## External Javascript files ##

	$javascript = $metaTags->getJavascript($pixout_required, $jw_player_required, $lightboxRequired);
	
	## Page title ##

	$page_title = SITENAME . " - " . $reg->findSetting('slogan');

	## final call for changes from plugins ##

	$h = $plugins->event('onAllPageDataReady');

	if ($h)
	{
		foreach ($h as $j) rewriteVariables($j);
	}

	## possible theme-specific changes ##

	if (file_exists("podhawk/custom/themes/" . $theme . "/template_functions.php"))
	{
		include ("podhawk/custom/themes/" .$theme . "/template_functions.php");
	}

	## assign variables to Smarty ##

	// head section of template
	$smarty->assign(array(  'theme'				=> $theme,			
							'path_to_template'	=> "podhawk/custom/themes/".$theme,
							'namespaces' 		=> $namespaces,
							'css' 				=> $css,
							'metatags' 			=> $metatags,
							'javascript' 		=> $javascript,
							'page_title' 		=> $page_title
							));

	$transPages = (isset($trans['pages'])) ? $trans['pages'] : 'Pages';

	//nextpage/previouspage data
	$smarty->assign(array(	'nextpage'			=> $nextpage,
							'previouspage'		=> $previouspage,
							'next_page_url'		=> $nextpageurl,
							'home_page'			=> $homepage,	
							'previous_page_url'	=> $previouspageurl,
							'pagination_string'	=> $pagination->getPaginationString($transPages)
							));


	//list of categories
	$smarty->assign('category_list',$categories);

	//tags
	$smarty->assign(array(	'tag_list' 		=> $tag_list,
							'tag_weights' 	=> $tag_weights,
							'tag_links'		=> $tag_links));

	//authors
	$smarty->assign('authors', $authors);

	//pixelout player
	$smarty->assign('pixout_required', $pixout_required);		

	//information about website players
	$smarty->assign('players', $players);

	// other possible plugin events
	$smarty->assign('plugins_head_script', $plugins->event("addHeadScript"));
	$smarty->assign('plugins_body_script', $plugins->event("addBodyScript"));

	// any other page elements generated by plugins
	$smarty->assign('plugins_page_elements', $pluginsPageElements);

	
?>
