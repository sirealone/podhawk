{*  the right sidebar for the cleaker template  *}

<div id="sidebarRight" class="sidebar">

{*   the navigation block   *}

	<h2>{$trans.nav}</h2>
	{include file='common:sidebar_navblock.tpl'}

{*   list of categories   *}

	<h2>{$trans.categories}</h2>
	{include file='common:sidebar_category_list.tpl'}

{*   tagcloud   *}

	<h2>{$trans.tags}</h2>	
	{include file='common:sidebar_tagcloud.tpl' number=5}
	
{*   calendar   *}

	<h2>{$trans.calendar}</h2>	
	{include file='common:sidebar_calendar.tpl'}
	

</div> {*  close right sidebar  *}


