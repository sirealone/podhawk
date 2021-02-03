{*  nav links at bottom of page  -  cleaker   *}

<div class="navigation">
		<p>
		{if $previouspage == true}<a href="{$previous_page_url}" title="{$trans.previous_page}"><img src="{$path_to_template}/images/resultset_previous.png" 	align="top" /> {$trans.previous_page}</a>{/if}
		{if $nextpage == true}
		&nbsp;&nbsp;&nbsp; 
		<a href="{$next_page_url}" title="{$trans.next_page}">{$trans.next_page} <img src="{$path_to_template}/images/resultset_next.png" align="top" alt="next" /></a>{/if}</p>
 	</div>
