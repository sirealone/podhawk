{*   zigzag theme  -  posting date and title   *}

<div class="data">{$posting.posted|date_format:$settings.preferred_date_format}</div>
		<div class="titolpost">
		{if isset($smarty.get.id)}
		<h2 id="post-{$posting.id}">{$posting.title}</h2>
		{else}
		<h2 id="post-{$posting.id}"><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a></h2>
		{/if}
		</div>
		<small>{$posting.posted|date_format:$settings.preferred_date_format} {$trans.by} {$posting.author}</small> | <img src="{$path_to_template}/images/post.gif" alt="post" /> <small><a href="{$settings.url}/index.php?id={$posting.id}" rel="bookmark title='{$trans.link_posting}'">{$trans.link_posting}</a></small><div class="final"></div>
		
