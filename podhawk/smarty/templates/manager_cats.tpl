{*   template for authors2 page   *}

{include file='manager_head.tpl'}

<body id="cats">

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>

{if $admin == true}

<div class="unit">
<h2>{$trans.editcats}</h2>

<form action="index.php?page=cats&amp;do=savecats" method="post">
<input type="hidden" name="auth" value="{$cats_auth_key}" />

<table>
<tr><th>{$trans.catname}</th><th>{$trans.catdesc}</th><th>{$trans.hide}</th><th></th></tr>

{foreach from=$categories item=category}
    
<tr><td><input class="cat" type="text" value="{$category.name}" name="cat{$category.id}" /></td>    
<td><input class="desc" type="text" value="{$category.description}" name="desc{$category.id}" /></td>
<td><input class="hidecat" type="checkbox" value ="1" name="hide{$category.id}"{if $category.hide} checked="checked"{/if} /></td> 
<td class="right"><input onClick="return yesno('{$trans.deletecategory}')" type="submit" value="{$trans.delete}" name="del{$category.id}" />
</td></tr>
    
{/foreach}

<tr><td>
<input class="cat" name="newcat" type="text" value="" /></td>
<td><input class="desc" name="newdesc" type="text" value="" /></td>
<td><input class="hidecat" type="checkbox" name="newhide" value="1" /></td>
<td class="right">&lt;&lt;&nbsp;{$trans.addnew}&nbsp;</td></tr>
<tr class="last"><td colspan="3">{$trans.hide2}</td><td class="right"><input type="submit" value="{$trans.saveall}" /></td></tr>

</table>

</form>
</div>

<div class="unit">
<h2>{$trans.cattag}</h2>

<table><tr><th>{$trans.cat}</th><th>{$trans.newtag}</th><th></th></tr>
<form action="index.php?page=cats&amp;do=tagfromcat" method="post">
<input type="hidden" name="auth" value="{$cats_auth_key}" />
<tr><td>
<select name="catname" class="cat">
{foreach from=$categories item=category}
<option value="{$category.id}">{$category.name}</option>
{/foreach}
</select>
</td><td>
<input type="text" name="newtagname" value="" class="tag" />
</td><td>
<input type="submit" value="{$trans.make}" />
</td></tr>
</table>
</form>
<p>{$trans.cattag_help}</p>
</div>

{literal}<script>
function selectItem(li) {
}

function formatItem(row) {
	return row;
}

$(document).ready(function() {
	$("#suggest").autocomplete('index.php?page=autocomplete&type=tags', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1, mode:"multiple",multipleSeparator:" " });
});
</script>{/literal}


<div class="unit">
<h2>{$trans.managetags}</h2>
<table><tr><th>{$trans.tag}</th><th>{$trans.action}</th><th>{$trans.newname}</th></tr>
<form action="index.php?page=cats&amp;do=edittag" method="post">
<input type="hidden" name="auth" value="{$cats_auth_key}" />
<tr><td>
<input id="suggest" type="text" class="cat" name="tagnames" value="" />

</td><td><input type="radio" name="tagaction" value="deletetag">{$trans.deletetag}</td><td></td></tr>

<tr><td></td><td><input type="radio" name="tagaction" value="amendtag">{$trans.replacetag}&nbsp;&gt;&gt;&gt;</td>

<td><input type="text" name="newtagname" class="tag" /></td></tr>
<tr><td></td><td><input type="radio" name="tagaction" value="createcat">{$trans.tagtocat}&nbsp;&gt;&gt;&gt;</td>
<td><select name="catname" class="tag">

{foreach from=$categories item=category}
<option value="{$category.id}">{$category.name}</option>
{/foreach}
      
</select></td></tr>
<tr><td></td><td></td><td><input onClick="return yesno('{$trans.confirmchange}')" type="submit" value="Submit" /></td></tr>
</form></table>
<p>{$trans.managetags_help}</p>
</div>

{/if}  {*  close 'admin only' condition  *}

</div> <!-- close content -->

{include file='manager_footer.tpl'}

