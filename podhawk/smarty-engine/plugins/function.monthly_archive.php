<?php

// a PodHawk plugin
// creates and assigns to Smarty an array containing the months in which postings were created
//
// Example use in template :
//
//<ul>
//{monthly_archive}
//{foreach from=$monthly_archive item=mo}
//
//<li><a href="podcast.php?date={$mo|date_format:'%Y-%m'}" title="RSS feed"><img src="{$path_to_template}/images/rss.gif" alt="RSS" /></a> <a href="index.php?date={$mo|date_format:'%Y-%m'}" title="Postings from {$mo|date_format:'%b %Y'}">{$mo|date_format:"%B %Y"}</a></li>
//
//{/foreach}
//</ul>


function smarty_function_monthly_archive ($params, &$smarty)  {

$dosql = "SELECT posted FROM ".DB_PREFIX."lb_postings WHERE status = '3' ORDER BY posted DESC";
$rows = $GLOBALS['lbdata']->GetArray($dosql);

$i=0;
$prevmonth = "0000-00";
foreach ($rows as $date)
	{
		$month = substr($date['posted'],0,7);
		if ($month == $prevmonth)
		{
			continue;
		}
		$montharray[$i] = $month."-01";
		$prevmonth = $month;
		$i++;
	}

$smarty->assign(array(	'monthly_archive' 		=> $montharray,
						'monthly_archive_count' => count($montharray)));



}
?>
