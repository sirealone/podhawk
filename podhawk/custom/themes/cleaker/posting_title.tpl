{*  cleaker template - title and date   *}

		{if isset($smarty.get.id)}
			<h1>{$posting.title}</h1>
		{else}
			<h1><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a></h1>
		{/if}
		<h4><img src="{$path_to_template}/images/calendar.png" align="top" alt="Date" /> {$posting.posted|date_format:$settings.preferred_date_format}</h4>
