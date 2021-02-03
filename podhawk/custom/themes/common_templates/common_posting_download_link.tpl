{ * puts 'download mp3' or similar link below posting * }

{if $posting.show_download_link == true}

{if $settings.template_language == 'deutsch'}
<{#download_link_style#|default:'p'}><a href="{$posting.web_link}">&nbsp;{$posting.mediatypename} {if #say_get#}{$trans.get}{else}{$trans.download}{/if} ({$posting.audio_size|getmegabytes} MB | {$posting.audio_length|getminutes} min)</a></{#download_link_style#|default:'p'}>
{else}
<{#download_link_style#|default:'p'}><a href="{$posting.web_link}">&nbsp;{if #say_get#}{$trans.get}{else}{$trans.download}{/if} {$posting.mediatypename} ({$posting.audio_size|getmegabytes} MB | {$posting.audio_length|getminutes} min)</a></{#download_link_style#|default:'p'}>
{/if}

{if $barcode == true}

<p><a href="javascript:void(null);" onclick="display_qr('{$settings.url|urlencode}', '{$posting.web_link|urlencode}', {$key});"> Download to your SmartPhone</a><span id="qr{$key}"></span></p>
{/if}

{/if}

{*  configuration variables

download_link_style is <p> by default, but can be set to eg <h4> in configuration file

say_get - download link says "Download MP3" by default. Set say_get = true to make link say "Get MP3"

*}
