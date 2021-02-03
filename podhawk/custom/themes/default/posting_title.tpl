{*  default theme - posting title  *}

{if isset($smarty.get.id)}
	<h2>{$posting.title}</h2>
{else}
	<h2><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a></h2>
{/if}
