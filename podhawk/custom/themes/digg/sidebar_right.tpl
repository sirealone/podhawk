<div class="sidebar">

<ul>

{*   subscribe links *}

<li><h2>{$trans.subscribe}</h2>
	{include file='common:sidebar_subscribe.tpl'}
</li>

{*  search box   *}

<li>
<h2>{$trans.search}</h2>
	{include file='common:sidebar_search.tpl'}
</li>	
	
{*  recent postings  *}

<li>
<h2>{$trans.recent_postings}</h2>
	{include file='common:sidebar_recent_postings.tpl number=5}
</li>

<li>
<h2>{$trans.categories}</h2>
	{include file="common:sidebar_category_list.tpl"}
</li>


</ul>

</div>
