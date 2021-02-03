{*  tagcloud  *}

<div{#tagcloud_div#|default:' id="tagcloud"'}>

{if isset($number)}
	{top_tags number=$number}
{/if}

{#tagcloud_container#|default:'<p>'}
{foreach from=$tag_list item=tag}
<span class="tagweight{$tag_weights[$tag]}"><a href="{$tag_links.$tag}" title="{$trans.linked_tags} {$tag|replace:'_':' '}">{$tag|replace:'_':' '} </a></span>
{/foreach}
{#tagcloud_container_close#|default:'</p>'}
</div>

{* configuration variables

tagcloud_container and tagcloud_container_close - the html tags which surround the tagcloud - defaults to <p>....</p>

*}
