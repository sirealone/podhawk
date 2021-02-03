	{*  default theme - left container  *}

	<div class="container">

        	<h2>{$trans.welcome}</h2>

	{*  description of website   *}

        <p>{$settings.description}</p>

	{*   links to rss feeds  *}

        <p><a href="{$rss_feed}"><img src="{$path_to_template}/images/feedicon.gif" alt="Feed icon" /> {$trans.podcast_feed}</a> 
        <br />
        <a href="{$rss_comment_feed}"><img src="{$path_to_template}/images/feedicon.gif" alt="Feed icon" /> {$trans.podcast_feed_comments}</a></p>

    	</div>
