	{*   cleaker theme - download counter   *}

{if $posting.show_downloads == true}
	{if ($mp3_only == false || $posting.mediatypename == 'MP3')}

		{if $settings.template_language == 'deutsch'}
		<p>{$trans.this_file} {insert name="downloads" id=$key} {$trans.times} {$trans.downloaded}</p>

		{else}

		<p>{$trans.downloaded} {insert name="downloads" id=$key} {$trans.times}</p>
		{/if}

	{/if}
{/if}
