{*  black urban sidebar  *}



		<ul>

			{*  search  *}
			
			<li>
			<h3>{$trans.search}</h3>
			{include file='common:sidebar_search.tpl'}
			</li>

			<li>
			<h3>Categories and Tags</h3>
			{include file="common:sidebar_cat_tags.tpl"}
			</li>

			{*  monthly archive  *}
			<li>
				<h3>{$trans.archive}</h3>
				{include file='common:sidebar_archive.tpl' since='2008-01'}
			</li>

			{*  calendar  *}
			<li>
				<h3>{$trans.calendar}</h3>
				{include file='common:sidebar_calendar.tpl'}
			</li>

			{*  subscribe  *}
			<li>	
				<h3>{$trans.subscribe}</h3>
				{include file='common:sidebar_subscribe.tpl'}
			</li>


			{*  tags  *}
			<li>
				<h3>{$trans.tags}</h3>
				{include file='common:sidebar_tagcloud.tpl'}
			</li>

			{*  recent postings  *}
			<li>
				<h3>{$trans.recent_postings}</h3>
				{include file='common:sidebar_recent_postings.tpl' number=5}
			</li>
				
            		{*  recent comments  *}
			<li>
				{include file='common:sidebar_recent_comments.tpl' number=5}
			</li>
			
			


		</ul>
	
