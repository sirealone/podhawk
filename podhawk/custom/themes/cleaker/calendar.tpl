{*   version of the calendar template for cleaker   *}


<div id="calendar">
	
	{podhawk_calendar}

{html_table loop=$calendar cols={$trans.days_string} caption=$calendar_this_month|date_format:"%B %Y" table_attr='border="0px"' th_attr='style="font-weight:bold"'}
<p class="center"><a href="{$calendar_previous_month_url}" title="{$calendar_previous_month|date_format:"%B %Y"}">&lt;&lt;{$calendar_previous_month|date_format:"%b"}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$calendar_next_month_url}" title="{$calendar_next_month|date_format:"%B %Y"}">{$calendar_next_month|date_format:"%b"}&gt;&gt;</a></p>

</div>
