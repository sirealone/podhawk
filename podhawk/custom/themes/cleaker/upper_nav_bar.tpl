{*   the navigation baracroos the top of the page  - cleaker  *}

<ul id="Nav">

	{if $nextpage == true}<li><a href="{$next_page_url}" title="{$trans.next_page}">&nbsp;{$trans.next} &gt;&gt;&nbsp;</a></li>{/if}
	{if $previouspage == true}<li><a href="{$previous_page_url}" title="{$trans.previous_page}">&nbsp;&lt;&lt; {$trans.previous}&nbsp;</a></li>{/if}

	<!--  you can change the links below to links of your own choice, and add further links if you wish  -->

	<li><a href="http://news.bbc.co.uk/">BBC News</a></li>
	<li><a href="http://www.podhawk.com/">PodHawk</a></li>
	<li><a href="index.php" title="{$trans.home_page}">{$trans.home}</a></li>    
</ul> 

