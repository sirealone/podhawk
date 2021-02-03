{*  lists all posts from a given category or with a particular tag  *}

{* parameters passed to Smarty plugin function smarty_function_cat_tag_list()
	type - cat or tag - do you want posts from a particular category or with a particular tag - defaults to cat
	name - name of category/tag required - get multiple tags thus tag1+tag2+tag3 
	alpha - true = order alphabetically by post title
			false = order by descending or of posting date
			defaults to true			*}

{posts_from_cat_tag type=$type name=$name alpha=$alpha}

{if isset($postings_cat_tag)}

	<ul>
	{foreach from=$postings_cat_tag item=post_cat_tag}
		<li><a href="{$post_cat_tag.permalink}">{$post_cat_tag.title}</a></li>
	{/foreach}
	</ul>

{else if isset($posts_from_cat_tag_error_message)}

	{$posts_from_cat_tag_error_message}

{/if}
