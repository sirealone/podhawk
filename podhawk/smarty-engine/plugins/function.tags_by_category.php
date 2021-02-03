<?php

function smarty_function_tags_by_category($params, &$smarty)
{
	$reg = Registry::instance();
	$categories = $reg->getCategoriesArray();

	foreach ($categories as $category)
	{
		$t = TagManager::instance();

		$tags = $t->getTagsForCategory($category['id']);

		foreach ($tags as $tag)
		{
			$cat_tags[$category['name']][] = $tag;
			$cat_encoded = rawurlencode(my_html_entity_decode($category['name']));
			$tag_encoded = rawurlencode(my_html_entity_decode($tag));
			$cat_tag_links[$category['name']][$tag] = 'index.php?cat=' . $cat_encoded .'&amp;tag=' . $tag_encoded;
		}
	}

	$smarty->assign (array(	'cat_tags' 		=> $cat_tags,
							'cat_tag_links' => $cat_tag_links));

}

?>
