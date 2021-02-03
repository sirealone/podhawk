{* lists the categories to which a posting belongs   *}

{if !#hash_link#}<p>{/if}{if #hash_link#}<a href="{$settings.url}" title="Permalink">#</a> | {$trans.posted_in}{else}{#post_cat_image#|default:''}{$trans.categories}{/if}{#spacer#|default:' '}{foreach from=$posting_categories.$key item=category name=post_categories}{if empty($category.hide)}<a href="{$category.link}" title="{$trans.link_categories} {$category.name}">{$category.name}</a>{if $smarty.foreach.post_categories.last == false}{#list_divider#|default:' | '}{/if}{/if}{/foreach}{if !#hash_link#}</p>{/if}

{* configuration file variables

post_cat_image - html image tag for any image which should appear at the beginning of the 'categories' list.
Defaults to no image tag.

spacer - by default there is only a space between 'categories' and the start of the list. Configuration file 
may however set spacer to eg ': ' or '- '

list_divider - what should appear between items in the list of categories eg ' * ', '  ' (double space). Defaults to ' | '

hash_link - if set to true in the config file, the category list will start with a '#' linked to the site url, and the words "Posted in ..". Will also suppress the <p>..</p> tags around the list of categories.

*}
