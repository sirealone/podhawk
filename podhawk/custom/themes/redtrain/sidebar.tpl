{*  red train  - sidebar   *}

<div id="navi">
		<div id="navi-innen">

			{*  about this site  *}

			<h2>{$trans.about_site}</h2>

			{include file='common:sidebar_about.tpl'}
		
			{*  list of categories  *}

			<h2>{$trans.categories}</h2>

			{include file='common:sidebar_category_list.tpl'}

			{*   tags   *}
		
			<h2>{$trans.tags}</h2>
	
			{include file='common:sidebar_tagcloud.tpl'}

			{*  recent postings  *}

			<h2>{$trans.recent_postings}</h2>

			{include file='common:sidebar_recent_postings.tpl' number=5}

			{*  recent comments  *}

			{include file='common:sidebar_recent_comments.tpl' number=5}

			{*  count visitors   *}

			{include file='common:sidebar_visitors.tpl'}


</div><!--Ende Navi innen-->
	</div><!--Ende Navi-->
