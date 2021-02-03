

		{monthly_archive}
		
			{if $monthly_archive|@count > 0}
			
			{foreach from=$monthly_archive item=month}
			{if !isset($since) || (isset($since) && $month >= $since)}
			

			<a href="podcast.php?date={$month|date_format:"%Y-%m"}"><img src="{$path_to_template}/images/rss.png" alt="RSS" border="0" align="top" /></a> 
			<a href="index.php?date={$month|date_format:"%Y-%m"}">{$month|date_format:"%B %Y"}</a><br /><br />
			
			{/if}
			{/foreach}

			{/if}	

