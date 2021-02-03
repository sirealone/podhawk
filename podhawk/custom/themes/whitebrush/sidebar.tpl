<div id="sidebar">


			{*  calendar  *}
			<div class="side">
			<!-- <h2>{$trans.calendar}</h2> -->			
			{include file='sidebar_calendar.tpl'}
			{include file='sidebar_search.tpl'}
			</div>	
								

			{*  subscribe  *}
			<div class="side" >
			<h2>&nbsp;</h2>
			<div class="scontent">
			<a href="{$rss_feed}" title="{$trans.rss_link}"><img src="{$path_to_template}/images/rss-feed.png" alt="rss" /></a>
			</div>
			</div>
			
			
			{*  recent postings  *}
			<div class="side">
			<h2>{$trans.recent_postings}</h2>
			<div class="scontent flexcroll autom">		
			{include file='common:sidebar_recent_postings.tpl' number=10}
			</div>
			</div>


			{*  categories  *}
			<div class="side">
			<h2>{$trans.categories}</h2>
			<div class="scontent flexcroll autom">
			{include file='common:sidebar_category_list.tpl'}
			</div>
			</div>


			{*  tags  *}
			<div class="side">
			<h2>{$trans.tags}</h2>	
			<div class="scontent">		
			{include file='common:sidebar_tagcloud.tpl' number=10}
			</div>
			</div>
			
			
			{* Mail  *}
			<div class="side">
			<h2>&nbsp;</h2>
			<div class="scontent">
			{include file='sidebar_email.tpl'}
			</div>
			</div>
			
			
			{*  monthly archive  *}
			<div class="side">
			<h2>{$trans.archive}</h2>
			<div class="scontent flexcroll autom">
			{include file='sidebar_archive.tpl'}
			</div>
			</div>
			
			
			{*  recent comments  *}
            <div class="side">
           	<h2>{$trans.recent_comments}</h2>
           	<div class="scontent flexcroll autom">
			{include file='common:sidebar_recent_comments.tpl' number=3}
			</div>
			</div>


	</div>  <!-- close sidebar -->
	
