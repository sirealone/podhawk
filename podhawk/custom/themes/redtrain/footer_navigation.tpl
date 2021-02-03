{*  red train - navigation at foot of page  *}

<div id="prevnext">
			<p>
	{if $previouspage == true}<a href="{$previous_page_url}" title="{$trans.previous_page}">&lt;&lt; {$trans.previous_page}</a>{/if}
	{if $nextpage == true}
	&nbsp;&nbsp;&nbsp; 
	<a href="{$next_page_url}" title="{$trans.next_page}">{$trans.next_page} &gt;&gt;</a>{/if}</p>
		</div>
