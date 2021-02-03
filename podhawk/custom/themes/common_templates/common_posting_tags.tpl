	{*  list of tags for each posting    *}

{if !#hash_link#}<p>{/if}{#post_cat_image#|default:''}{$trans.tags}{#spacer#|default:' '}{foreach from=$posting_tags.$key item=tag name=post_tags}
<a href="{$posting_tag_links.$key.$tag}" title="{$trans.link_tags} {$tag|replace:'_':' '}">{$tag|replace:'_':' '}</a>{if $smarty.foreach.post_tags.last == false}{#list_divider#|default:' | '}{/if}{/foreach}{if !#hash_link#}</p>{/if}

{* configuration file variables

post_cat_image - html image tag for any image which should appear at the beginning of the 'tags' list.
Defaults to no image tag.

spacer - by default there is only a space between 'tags' and the start of the list. Configuration file 
may however set spacer to eg ': ' or '- '

list_divider - what should appear between items in the list of tags eg ' * ', '  ' (double space). Defaults to ' | '

hash_link - if 'true', suppresses the <p>..</p> tags around the list of tags

*}
