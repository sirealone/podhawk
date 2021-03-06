{*  kubrick sidebar  *}

<div id="sidebar">

		<ul>

			{* the 'about this site' section  *}
			<li>
				<h2>{$trans.about}</h2>
				{include file='common:sidebar_about.tpl'}
			</li>

			{*  monthly archive  *}
			<li>
				<h2>{$trans.archive}</h2>
				{include file='common:sidebar_archive.tpl' since='2008-01'}
			</li>

			{*  calendar  *}
			<li>
				<h2>{$trans.calendar}</h2>
				{include file='common:sidebar_calendar.tpl'}
			</li>

			{*  subscribe  *}
			<li>	
				<h2>{$trans.subscribe}</h2>
				{include file='common:sidebar_subscribe.tpl'}
			</li>

			{*  categories  *}
			<li>
				<h2>{$trans.categories}</h2>
				{include file='common:sidebar_category_list.tpl'}
			</li>

			{*  tags  *}
			<li>
				<h2>{$trans.tags}</h2>
				{include file='common:sidebar_tagcloud.tpl'}
			</li>

			{*  recent postings  *}
			<li>
				<h2>{$trans.recent_postings}</h2>
				{include file='common:sidebar_recent_postings.tpl' number=5}
			</li>
				
            		{*  recent comments  *}
			<li>
				{include file='common:sidebar_recent_comments.tpl' number=5}
			</li>
			
			{*  search  *}
			
			<li>
			<h2>{$trans.search}</h2>
			{include file='common:sidebar_search.tpl'}
			</li>


		</ul>
	</div>  <!-- close sidebar -->
	
