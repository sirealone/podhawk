   {*   template for backend find postings page   *}

{include file='manager_head.tpl'}

<body id="find">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{if isset($trans.$message)}{$trans.$message}{else}{$message}{/if}</p>{/if}

<div class="unit">
<h3>{$trans.findid}</h3>
<form action="index.php" method="get">
<table>
<tr><td class="left">{$trans.findidlabel}</td>
<td class="centre"><input type="hidden" name="page" value="record2" /><input type="hidden" name="do" value="edit" /><input type="text" name="id" class="id" value="" /><input type="hidden" name="auth" value="{$record2_auth_key}" /></td>
<td class="right"><input type="submit" value="{$trans.findpost}" /></td>
</tr>
</table>
</form>
</div>

<div class="unit">
<h3>{$trans.findmonth}</h3>
<form action="index.php?page=postings" method="post" enctype="multipart/form-data">
<table>
<tr><td class="left">{$trans.month}{html_options name=month options=$months selected=$this_month}</td><td class="centre">{$trans.year}{html_options name=year options=$years selected=$this_year}</td>
<td class="right"><input type="submit" value="{$trans.findposts}" /></td></tr>
</table>
</form>
</div> 

<div class="unit">
<h3>{$trans.findauthor}</h3>
<form action="index.php?page=postings" method="post">
<table><td class="left">{$trans.author} {html_options name=author options=$authors}</td><td class="centre"><td>
<td class="right"><input type="submit" value="{$trans.findposts}" /></td></tr>
</table>
</form>
</div> 

<div class="unit">
<h3>{$trans.findcat}</h3>
<form action="index.php?page=postings" method="post" enctype="multipart/form-data">
<table>
<tr><td class="left">{$trans.cat} {html_options name=cat options=$categories}</td><td class="centre"></td>
<td class="right"><input type="submit" value="{$trans.findposts}" /></td></tr>
</table>
</form>
</div> 

{literal}<script>
function selectItem(li) {
}

function formatItem(row) {
	return row;
}

$(document).ready(function() {
	$("#tag").autocomplete('index.php?page=autocomplete&type=tags', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1 });
});
</script>{/literal}

<div class="unit">
<h3>{$trans.findtag}</h3>
<form action="index.php?page=postings" method="post" enctype="multipart/form-data">
<table>
<tr><td class="left">{$trans.tag} <input id="tag" type="text" name="tag" value="" /></td>
<td class="centre"></td>
<td class="right"><input type="submit" value="{$trans.findposts}" /></td></tr>
</table>
</form>
</div> 

<div class="unit">
<h3>{$trans.findtitle}</h3>
<form action="index.php?page=postings" method="post" enctype="multipart/form-data">
<table>
<tr><td class="left">{$trans.titlehelp1}</td>
<td class="centre"><input type="text" name="title1" value="" /></td>
<td class="right"><input type="submit" value="{$trans.findposts}" /></td></tr>
</table></form>
<form action="index.php?page=postings" method="post" enctype="multipart/form-data">
<table>
<tr><td class="left">{$trans.titlehelp2}</td>
<td class="centre"><input type="text" name="title2" value="" /></td>
<td class="right"><input type="submit" value="{$trans.findposts}" /></td></tr>
</table>
</form>
</div> 

</div> <!--  close content  -->
{include file='manager_footer.tpl'}
