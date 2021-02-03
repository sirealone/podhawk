{*  digg template - puts the title, date and author on the webpage   *}

		{if isset($smarty.get.id)}
			<h2>{$posting.title}</h2>
		{else}
			<h2><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a></h2>
		{/if}
		<div class="postinfo">{$trans.posted_by} {$posting.author} {$trans.on} <span class="postdate">{$posting.posted|date_format:$settings.preferred_date_format}</span></div>
		
