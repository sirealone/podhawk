<div id="calendar">
{*   places calendar in sidebar
   
Optional parameters for podhawk_calendar

 'user_defined_months' 	true = uses month names from an array in the language file for the theme
						false = uses system names for months
 'categories' 	true = if the webpage displays only posts from a specified category, the calendar will show only posts in that category
				false = calendar will always show all posts
 'tags'			true = if the webpage displays only posts with a specified tag, the calendar will show only posts with that tag
				false = calendar will always show all posts
 'authors'		true = if the webpage displays anly posts by a specified author, the calendar will show only posts by that author
				false = calendar will alwats show all posts *}

{podhawk_calendar user_defined_months=false categories=false tags=false authors=false}

{html_table loop=$calendar cols=$trans.days_string caption=$calendar_this_month table_attr='border="0px"' th_attr='style="font-weight:bold"'}

<a href="javascript:void(null)" title="{$calendar_previous_month}" onclick='updateCalendar("{$calendar_previous_month_url}", {$gets})'> &lt;&lt;&lt; </a> 
<a href="javascript:void(null)" title="{$calendar_next_month}" onclick='updateCalendar("{$calendar_next_month_url}", {$gets})'> &gt;&gt;&gt; </a>
</div>


