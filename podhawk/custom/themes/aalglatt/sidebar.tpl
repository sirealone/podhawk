{*  sidebar template for aalglatt theme  *}

<div id="right">

{*  information about the site or the author  *}
	<div id="author">
		<h3>{$trans.the_info}</h3>
		<p>{$trans.info_about_me}</p>
	</div>

{*the following empty 'div' is used to put the green divider between units *}
<div class="line"></div>

<div id="links">

<div id="pages">

	{*  navigation  *}
	<h3>{$trans.the_pages}</h3>

		{include file='common:sidebar_navblock.tpl'}

		{include file='common:sidebar_subscribe.tpl' with_categories=true}

	<div class="line"></div>

	{* google search  *}
	<h3>{$trans.the_search}</h3>
	<br />
		{include file='common:sidebar_search.tpl'}
	<br />
	<div class="line"></div>

	{*  list of authors  *}
	<h3>{$trans.the_assocs}</h3>

		{include file='common:sidebar_authors.tpl' with_email = true}

	<div class="line"></div>

	{*  monthly archive  *}
	<h3>{$trans.the_storage}</h3>

		{include file='common:sidebar_archive.tpl' since=2004-05}

	<div class="line"></div>

	{*  list of categories  *}
	<h3>{$trans.the_categories}</h3>

		{include file='common:sidebar_category_list.tpl'}

	<div class="line"></div>

	{*  tag cloud  *}
	<h3>{$trans.the_tags}</h3>

		{include file='common:sidebar_tagcloud.tpl'}

	<div class="line"></div>

	{*  calendar  *}
	<h3>{$trans.the_dates}</h3>

		{include file='common:sidebar_calendar.tpl'}

	<div class="line"></div>

	{*  recent comments - heading is in the recent_comments.tpl file  *}

		{include file='common:sidebar_recent_comments.tpl' number=5 with_message=true}
	<br />
	<div class="line"></div>

	{* recent postings *}
	<h3>{$trans.the_postings}</h3>

		{include file='common:sidebar_recent_postings.tpl'}

	<div class="line"></div>

	<h3>{$trans.the_visitors}</h3>
	
	{include file='common:sidebar_visitors.tpl' minutes=5}
</div></div></div>
