{*   kubrick theme  -  posting date and title   *}

		{if isset($smarty.get.id)}
		<h2>{$posting.title}</h2>
		{else}
		<h2><a href="{$posting.permalink}" title="Link to this posting">{$posting.title}</a></h2>
		{/if}
		<small>{$posting.posted|date_format:$settings.preferred_date_format} {$trans.by} {$posting.author}</small>
