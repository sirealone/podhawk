<?php

// A PodHawk plugin
//
// generates the data for a calendar, and assigns it to Smarty
//
// Optional parameters ;
//  'user_defined_months' 	true = uses month names from an array in the language file for the theme
//							false = uses system names for months
//  'categories' 	true = if the webpage displays only posts from a specified category, the calendar will show only posts in that category
//					false = calendar will always show all posts
//	'tags'			true = if the webpage displays only posts with a specified tag, the calendar will show only posts with that tag
//					false = calendar will always show all posts
//	'authors'		true = if the webpage displays anly posts by a specified author, the calendar will show only posts by that author
//					false = calendar will alwats show all posts
//
// Example use in template :
//
// {podhawk_calendar user_defined_months=true categories=false}
// {include file='calendar.tpl}

function smarty_function_podhawk_calendar ($params, &$smarty)
{
	// get a list of user defined names of months
	if (!empty($params['user_defined_months']))
	{
		$reg = Registry::instance();

		$t = new TR_TranslationWebpage($reg->findSetting('template'));

		$monthNames = $t->getTrans('monthNames');
	}

	if (isset($_GET['cal']))
	{
		$date 		= $_GET['cal'];
		$separator 	= strpos($date, '-');
		$year		= substr($date, 0, $separator);
		$month 		= substr($date, $separator + 1,2);
	}
	elseif (isset($_GET['date']))
	{
		$date 		= $_GET['date'];
		$separator 	= strpos($date, '-');
		$year 		= substr($date, 0, $separator);
		$month 		= substr($date, $separator + 1,2);
	}
	else
	{
		$month 		= date('m');
		$year 		= date('Y');
	}

	$firstDayOfWeek 	= 1;  //Monday
	$firstDay 			= mktime(0, 0, 0, $month, 1, $year);
	$dayOfWeek 			= (date('w', $firstDay) + 7 - $firstDayOfWeek) % 7;
	$lastDay 			= mktime(0, 0, 0, $month + 1, 0, $year);
	$maxDays 			= date('d', $lastDay);

	// this month (ie the month to be displayed)...
	$thisMonth 			= $year."-".$month;

	// ... and the full definition of the beginning and the end of $thisMonth
	$start 				= $thisMonth."-01 00:00:00";
	$end 				= $thisMonth."-".$maxDays . " 23:59:59";

	// previous month
	$previousMonth 		= date ("Y-m",(mktime(0, 0, 0, $month - 1, 1, $year)));

	// next month
	$nextMonth 			= date("Y-m",(mktime(0, 0, 0, $month + 1, 1, $year)));	

	// how many leading spaces before the first day of the month?
	$leading=array();

	for ($i=0;$i<$dayOfWeek;$i++)
	{
		$leading[$i] = "&nbsp;";
 	}

	for ($i=0;$i<$maxDays;$i++)
	{
		$dates[$i] = $i+1;
	}


	$cats = !empty($params['categories']);
	$tags = !empty($params['tags']);
	$authors = !empty($params['authors']);

	$pagination = new PO_Pagination_Calendar($start, $end, $cats, $tags, $authors);

	$rows = $pagination->getRows();
	
	foreach ($rows as $row)
	{
		$postingday = substr($row['posted'],8,2);
		//strip leading 0 from date for display in the calendar, but not from the link
		$postingdayx = (substr($postingday,0,1) == "0") ? substr($postingday,1,1) : $postingday;
		
		$dates[$postingday-1] = "<a href=\"index.php?date=".$year."-".$month."-".$postingday."\" title=\"".$row['title']."\">".$postingdayx."</a>";
	}

	//put 'today' between span tags for styling
	if ($year == date('Y') && $month == date('m'))
	{
		$today 				= date('j');
		$dates[$today-1] 	= '<span class="today">'.$dates[$today-1]."</span>";
	}

	
	$calendar = array_merge($leading,$dates);

	$this_month_unix = mktime(0, 0, 0, $month, 1, $year);
	$previous_month_unix = mktime(0, 0, 0, $month-1, 1, $year);
	$next_month_unix = mktime(0, 0, 0, $month+1, 1, $year);

	if (!empty($params['user_defined_months']))
	{
		$calendar_this_month = $monthNames[date('m', $this_month_unix)]['long'] . ' ' . date('Y', $this_month_unix);
		$calendar_previous_month = $monthNames[date('m', $previous_month_unix)]['long'] . ' ' . date('Y', $previous_month_unix);
		$calendar_next_month = $monthNames[date('m', $next_month_unix)]['long'] . ' ' . date('Y', $next_month_unix);

		$calendar_previous_month_short = $monthNames[date('m', $previous_month_unix)]['short'];
		$calendar_next_month_short = $monthNames[date('m', $next_month_unix)]['short'];
	}
	else
	{
		$calendar_this_month 		= date("F Y",$this_month_unix);
		$calendar_previous_month 	= date("F Y",$previous_month_unix);
		$calendar_next_month 		= date("F Y",$next_month_unix);

		$calendar_previous_month_short = date('M', $previous_month_unix);
		$calendar_next_month_short = date('M', $next_month_unix);
		
	}

	$cat = (isset($_GET['cat'])) ? $_GET['cat'] : '';
	$tag = (isset($_GET['tag'])) ? $_GET['tag'] : '';
	$author = (isset($_GET['author'])) ? $_GET['author'] : '';
	$theme = (isset($_GET['theme'])) ? $_GET['theme'] : '';
	$gets = json_encode(array('cat' => $cat,'tag' => $tag,'author' => $author, 'theme' => $theme));


	$smarty->assign(array(	'calendar'						=> $calendar,
		      				'calendar_this_month'			=> $calendar_this_month,
							'calendar_next_month'			=> $calendar_next_month,
							'calendar_previous_month'		=> $calendar_previous_month,
							'calendar_next_month_url'		=> $nextMonth,
							'calendar_previous_month_url'	=> $previousMonth,
							'calendar_previous_month_short' => $calendar_previous_month_short,
							'calendar_next_month_short'		=> $calendar_next_month_short,
							'gets'							=> $gets));

}
?>
