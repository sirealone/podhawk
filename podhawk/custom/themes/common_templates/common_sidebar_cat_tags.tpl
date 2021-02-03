{*   lists tags ordered by categories   *}

{tags_by_category}

{if $category_list|@count > 0}

<ul>

{foreach from=$category_list item=category}
	{assign var='cat_name' value=$category.name}
	<li><a href="index.php?cat={$category.name|html_to_url}" title="{$trans.link_categories} {$category.name}">{$category.name}</a>
		{if $cat_tags.$cat_name|@count > 0}
		<ul>
		{foreach from=$cat_tags.$cat_name item=tag}
		<li><a href="{$cat_tag_links.$cat_name.$tag}" title="Postings in category {$category.name} tagged {$tag|replace:'_':' '}">{$tag|replace:'_':' '}</a></li>
		{/foreach}
		</ul>
		{/if}
	</li>
	{/foreach}
</ul>

{/if}




