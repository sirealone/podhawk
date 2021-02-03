{*  black urban template - puts the title of a posting on the webpage   *}

	<div class="post-title">
		{if isset($smarty.get.id)}
			<h2>{$posting.title}</h2>
		{else}
			<h2><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a></h2>
		{/if}
	<p>{$trans.posted_by} {$posting.author} {$posting.posted|date_format:$settings.preferred_date_format}</p><br />

	</div>
		
