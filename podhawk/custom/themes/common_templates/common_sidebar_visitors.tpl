{*  visitor counter  *}

{if $settings.count_visitors == true}

{if !isset($minutes) or $minutes == 60}
		<p>{$trans.visitors_last_hour} {insert name='visitors' seconds=3600}</p>
	{else}
		<p>{$trans.visitors_in_last} {$minutes} {$trans.minutes} {insert name='visitors' seconds = $minutes*60}</p>
	{/if}

{/if}
