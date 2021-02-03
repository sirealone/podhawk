	{*  black urban theme - navigation links at foot of page  *}

<div class="navigation">
            <div class="alignleft">{if $previouspage == true}<a href="{$previous_page_url}" title="{$trans.previous_page}">&lt;&lt; {$trans.previous_page}</a>{/if}</div>
            <div class="alignright">{if $nextpage == true}&nbsp;&nbsp;&nbsp;<a href="{$next_page_url}" title="{$trans.next_page}">{$trans.next_page} &gt;&gt;</a>{/if}</div>
            <div class="clear">&nbsp;</div>
        </div>
