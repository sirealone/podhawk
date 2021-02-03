{*  cleaker  left sidebar  *}

<div id="sidebarLeft" class="sidebar">

{*  the 'about me' section  *}
	<h2>{$trans.about_me}</h2>
	{include file='common:sidebar_about.tpl'}

{*  description of the site  *}
	<h2>{$trans.about_site}</h2>
	{include file='sidebar_site_description.tpl'}

{*  subscribe links  *}
	<h2>{$trans.subscribe}</h2>
	{include file='common:sidebar_subscribe.tpl'}

{*  list of recent postings  *}
	<h2>{$trans.recent_postings}</h2>
	{include file='common:sidebar_recent_postings.tpl' number=5 with_date=true with_author=true}

{*  recent comments list  *}
	{include file='common:sidebar_recent_comments.tpl' number=5}

{*  list of authors  *}
	<h2>{$trans.authors}</h2>
	{include file='common:sidebar_authors.tpl' with_email=true}

</div>  {*  close sidebarLeft  *}
    
