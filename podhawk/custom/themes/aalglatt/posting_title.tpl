{*  aalglatt template - puts the text of a posting on the webpage   *}

		{if isset($smarty.get.id)}
			<h2>{$posting.title}</h2>
		{else}
			<h2><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a></h2>
		{/if}
		<div class="meta">{$trans.posted_by} {$posting.author} on {$posting.posted|date_format:$settings.preferred_date_format}</div>
		
