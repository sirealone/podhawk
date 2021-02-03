<div class="obar">


<ul>

{*  navigation/list of pages  *}

<li>
<h2>{$trans.pages}</h2>

	{include file='common:sidebar_navblock.tpl'}

</li>

{*  calendar  *}

<li>
<h2>{$trans.calendar}</h2>

	{include file='common:sidebar_calendar.tpl'}

</li>

{*  archive  *}

<li>
<h2>{$trans.archive}</h2>

	{include file='common:sidebar_archive.tpl' since=2008-01}

</li>

{*  recent_comments  - the heading is in the sidebar_recent_comments template  *}

<li>

	{include file='common:sidebar_recent_comments.tpl' number=5}
</li>

<li>
<h2>Credits</h2>
	{include file='sidebar_credits.tpl}
</li>

</ul>
</div>
