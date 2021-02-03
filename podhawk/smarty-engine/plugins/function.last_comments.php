<?php

// a PodHawk plugin
//
// creates an array of data about the $number most recent comments
//
// You need to pass a value for $number to the function.
//
// Example of use in template :
//
// <ul>
// {last_comments number=5}
// {foreach from=$recent_comments item=comment name=recent_comments_loop}
//	<li><a href="index.php?id={$comment.posting_id}#com{$comment.id}" title="Link to this comment">{$comment.name} ({$comment.posting_title})</a></li>
// {/foreach}
// </ul>

function smarty_function_last_comments ($params, &$smarty)    {

$number = $params['number'];
$comments_table = DB_PREFIX . 'lb_comments';
$postings_table = DB_PREFIX . 'lb_postings';

$dosql = 	"SELECT $comments_table.*, $postings_table.title AS posting_title 
			FROM $comments_table, $postings_table
			WHERE $comments_table.posting_id = $postings_table.id ORDER BY $comments_table.posted DESC LIMIT $number";

$GLOBALS['lbdata']->prepareStatement($dosql);

$rows = $GLOBALS['lbdata']->executePreparedStatement(array());

foreach ($rows as $comment)
{
	
	$p = new PO_Permalink($comment['posting_id']);
	$link = array('posting_link' => $p->permalink());
	$recent_comments[] = array_merge($comment, $link);
	
}

$smarty->assign(array(	'recent_comments' 		=> $recent_comments,
						'recent_comments_count' => count($recent_comments)));
}
?>
