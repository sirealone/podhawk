{*   search form   *}

<form method="get" action="http://www.google.com/search">


<table border="0" cellpadding="0">
<tr><td>
<input{#search_input_class#|default:''} id="search-input" type="text" name="q" size="25"
 maxlength="255" value="" />
</td></tr>
<tr><td>
<input{#search_submit_class#|default:''} type="submit" id="search-submit" value="{$trans.google_search}" /></td></tr>
<tr><td id="this_site">
<input type="checkbox"  name="sitesearch"
 value="{$settings.url}" checked="checked" /> <small>{$trans.this_site_only}</small>
</td></tr></table>


</form>

{*
Configuration variables

search_input_class - class for 'input' (default '')
search_submit_class - class for submit button (default '')

*}
