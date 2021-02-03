{*  places list of authors in sidebar *}

<ul>

{foreach from=$authors item=author}

{if empty($author.hide)}
<li>
<a href="{$author.link}" title="All postings by {$author.nickname}">{$author.nickname}</a>{if $with_email == true && !empty($author.mail)} ({mailto address=$author.mail subject=$settings.sitename text='E-mail' encode='javascript'}){/if}	
</li>
{/if}

{/foreach}

</ul>
