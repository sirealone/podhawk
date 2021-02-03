{*  places a list of hyperlinks below the posting  *}

	{if $posting_links.$key|@count > 0}
		{if #hyperlinks_dl#}
			<dl>
			{foreach from=$posting_links.$key item=link}
			<dt><a href="{$link.url}"{if $new_tab == true} target="_blank"{/if}>{$link.title}</a></dt>
			<dd>{$link.description}</dd>
			{/foreach}
		</dl>
			
		{else}

		<div{#hyperlinks_class#|default:' class="links"'}>
		{foreach from=$posting_links.$key  item=link}		
			<p><a href="{$link.url}"{if $new_tab == true} target="_blank"{/if}>{$link.title}</a> : {$link.description}</p>	
		{/foreach}
		</div>
			
		{/if}
	{/if}

{*
Configuration variables

hyperlinks_dl - place links in a "definition list" structure; default is to place links in paragraphs.

hyperlinks_class - the 'id' or 'class' (if any) of the div which encloses the hyperlinks. Default is class="links"

*}
