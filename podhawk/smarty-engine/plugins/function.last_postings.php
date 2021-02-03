<?php

// a PodHawk plugin
// returns a list of $number most recent 'on air' postings
// You need to pass the following to the function :
// number - the number of recent postings which you want to show
//(optional) alpha - if you want the list in alphabetical order
//(optional) cat - if you want only postings from a particular category

// example use in template :
// <ul>
// {last_postings number=6}
//	{foreach from=$last_postings item=posting name=last_postings_loop}	
//		<li><a href="index.php?id={$posting.id}" title="Link to this posting">{$posting.title}</a></li>	
//	{/foreach}
// </ul>
// the plugin also assigns to smarty a value for the number of postings actually found -
// (last_postings_count)

function smarty_function_last_postings ($params, &$smarty)
{
	$number = (!empty($params['number'])) ? $params['number'] : 5;
	$alpha 	= (!empty($params['alpha'])) ? $params['alpha'] : false;
	$cat 	= (!empty($params['cat'])) ? $params['cat'] : NULL;
	$dateRange = (!empty($params['date_range'])) ? $params['date_range'] : '';

	$pagination = new PO_Pagination_LastPostings($number, $alpha, $cat, $dateRange);

	$rows = $pagination->getRows();

	foreach ($rows as $row)
	{
		$p = new PO_Posting_LastPostings($row);

		$p->extendPostingData();

		$last_postings[] = $p->getPosting();
	}
	
	$smarty->assign(array(	'last_postings' 		=> $last_postings,
							'last_postings_count' 	=> count($last_postings)));
}
?>
