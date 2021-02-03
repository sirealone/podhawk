{*   digg theme  - header search  *}

<form method="get" action="http://www.google.com/search">

<div>

<input type="text" name="q" size="25"
 maxlength="255" value="" id="s" />

<input type="submit" value="{$trans.this_site_search}" />

<input type="hidden"  name="sitesearch"
 value="{$settings.url}" /> 

</div>

</form>
