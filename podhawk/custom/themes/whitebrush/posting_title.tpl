		{if isset($smarty.get.id)}
		<h1>{$posting.title}</h1>
		{else}
		<h1><a href="{$posting.permalink}" title="Link to this posting">{$posting.title}</a></h1>
		{/if}
		<small>{$posting.posted|date_format:$settings.preferred_date_format} {$trans.by} {$posting.author}</small>
