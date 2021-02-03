{*   menus template   *}

<div id="navbar">
<form action="index.php?do=logout" method="post">
<input type="submit" value="Log out" class="wide" />
</form>
<br />

{if $page == 'record2'}
<h3>Editor</h3>
<form action="index.php?page=record2&amp;do=change_editor&amp;id={$id}" method="post">
<input type ="hidden" name="auth" value="{$record2_auth_key}" />
<select name="editor_requested">
<option value="1" {if $editor_to_use == 1}selected="selected"{/if}>Textile</option>
<option value="4" {if $editor_to_use == 4}selected="selected"{/if}>Tiny MCE</option>
<option value="5" {if $editor_to_use == 5}selected="selected"{/if}>Very Simple Editor</option>
<option value="6" {if $editor_to_use == 6}selected="selected"{/if}>Raw HTML</option>
</select> 
<input type="submit" value="{$trans.change_editor}" class="wide" />
</form>

	{if $editor_to_use == 1}
	<p>{$trans.you_are_using}<a href="http://www.textism.com/tools/textile/index.html" target="_blank"><b>Textile</b></a></p>
	{elseif $editor_to_use == 4}
	<p>{$trans.you_are_using}<a href="http://tinymce.moxiecode.com/index.php" target="_blank"><b>Tiny MCE</b></a></p>
	{elseif $editor_to_use == 5}
	<p>{$trans.you_are_using}<b>{$trans.very_simple}</b></p>
	{elseif $editor_to_use == 6}
	<p>{$trans.you_are_using}<b>{$trans.raw_html}</b></p>
	{/if}
<br />
{/if}

{foreach from=$menu key=heading item=items}
	<div class="menuItem">
	<h3>{$heading}</h3>
	<ul class="hidden">
	{foreach from=$items key=title item=href}
		<li><a href="{$href}">{$title}</a></li>
	{/foreach}
	</ul>
	</div>
{/foreach}

{if $page == 'record2'}
	<div class="menuItem">
	<h3>Recording page menu</h3>
	<ul class="hidden">
	{foreach from=$green_menu item=item}
	<li>{$item}</li>
	{/foreach}
	</ul>
	</div>
{/if}

</div>
