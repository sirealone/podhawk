{*  default theme - posting categories  *}

{$trans.categories}: {foreach from=$posting_categories.$key item=category}
{if empty($category.hide)}
<a href="{$category.link}" title="{$trans.link_categories} {$category.name}">{$category.name}</a> | {/if}{/foreach}
