<?php

// creates a list (title and permalink) of all posts in a particu;ar category
// or with a particular tag, ordered either by posting date
// or alphabetically by title

// $params['type'] - 'cat' or 'tag' - defaults to 'cat'
// $params['name'] - the name of the category or tag
// $params['order'] - bool - true = order alphabetically, false = order by date - defaults to true

// example of use in template

// {posts_from_cat_tag type=$type name=$name order=$order}
// <ul>
// {foreach from $postings_cat_tag as $post_cat_tag}
//	<li><a href="{$post_cat_tag.permalink}">{$post_cat_tag.title}</a></li>
// {/foreach}
// </ul>

function smarty_function_posts_from_cat_tag ($params, &$smarty)
{
	if (!isset($params['name'])) // we must have a name!
	{
		$smarty->assign ('posts_from_cat_tag_error_message', 'No value has been set for the name of the category or tag which you want.');
		return;
	}
	else
	{
		$name = $params['name'];
	}

	$type = (isset($params['type'])) ? $params['type'] : 'cat';
	$alpha = !empty($params['alpha']);

	$pagination = new PO_Pagination_PostsCatTag($type, $name, $alpha);

	$rows = $pagination->getRows();

	if (empty($rows))
	{
		$e = ($type == 'cat') ? "I could not find any posts in category $name" : "I could not find any posts with tag $name";
		$smarty->assign ('posts_from_cat_tag_error_message', $e);
		return;
	} 

	foreach ($rows as $row)
	{
		$array = array ('title' => $row['title'], 'permalink' => PO_Posting_Extended::getPermalink($row['id']));
		$postings_cat_tag[] = $array;
	}
	
	$smarty->assign('postings_cat_tag', $postings_cat_tag);

}
?>
		
	
