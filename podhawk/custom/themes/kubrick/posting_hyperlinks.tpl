{*  kubrick - posting hyperlinks  *}

{if $posting_links.$key|@count > 0}
	<dl>
		{foreach from=$posting_links.$key item=link}
		<dt><a href="{$link.url}"{if $new_tab == true} target="_blank"{/if}>{$link.title}</a></dt>
		<dd>{$link.description}</dd>
		{/foreach}
	</dl>
{/if}
