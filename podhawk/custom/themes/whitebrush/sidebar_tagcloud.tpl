<div id="tagcloud">

{if isset($number)}
	{top_tags number=10}
{/if}

<ul>
<li>
{foreach from=$tag_list item=tag}
<span class="tagweight{$tag_weights[$tag]}"><a href="{$tag_links.$tag}" title="{$trans.linked_tags} {$tag|replace:'_':' '}">{$tag|replace:'_':' '} </a></span>
{/foreach}
</li>
</ul>
</div>
