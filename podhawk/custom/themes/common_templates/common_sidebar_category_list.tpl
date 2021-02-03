{*  displays a list of the categories   *}

{if $category_list|@count > 0}
<ul>
{foreach from=$category_list item=category}
{if empty($category.hide)}
	<li><a href="{$category.link}" title="{$trans.link_categories} {$category.name}">{#cat_list_image#|default:''}{$category.name}</a></li>
{/if}
{/foreach}
</ul>
{/if}

{*
Configuration variables

cat_list_image - html image tag for any image to be displayed in front of each category link

*}
