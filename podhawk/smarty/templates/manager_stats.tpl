{*  template for statistics page   *}

{include file='manager_head.tpl'}

<body id="stats">
<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if isset($message)}<p class="msg">{$trans.$message}</p>{/if}

<form name="showform" action="index.php{add_to_url att='page' value='stats'}" method="post" enctype="multipart/form-data">
 <select id="chooser" onChange="document.showform.submit()" name="show">
      <option {if $show == 'tenpost'}selected="selected" {/if}value="tenpost">{$trans.tenpost}</option>
      <option {if $show == 'oneweek'}selected="selected" {/if}value="oneweek">{$trans.oneweek}</option>
      <option {if $show == 'onemonth'}selected="selected" {/if}value="onemonth">{$trans.onemonth}</option>
      <option {if $show == 'threemonth'}selected="selected" {/if}value="threemonth">{$trans.threemonth}</option>
      <option {if $show == 'oneyear'}selected="selected" {/if}value="oneyear">{$trans.oneyear}</option>
      <option {if $show == 'allpost'}selected="selected" {/if}value="allpost">{$trans.allpost}</option>
  </select>
</form>

<table>
<tr>
{foreach from=$headings key=heading item=criterion}
<th class="{if $sortby == $heading}pink {/if}{$heading}"><a href="index.php{add_to_url att='sort' value=$criterion}"><span>{$trans.$heading}</span></a></th>
{/foreach}
</tr>

{foreach from=$postings item=posting}
<tr>
<td class="date">{$posting.posted|strtotime|date_format}</td>    
<td><a href="index.php?page=record2&amp;auth={$record2_auth_key}&amp;do=edit&amp;id={$posting.id}">{$posting.title}</a></td>
<td class="number">{$posting.countweb}</td>
<td class="number">{$posting.countfla}</td>
<td class="number">{$posting.countpod}</td> 
<td class="number">{$posting.countall}</td>
</tr>
{/foreach} 

<div id="figure">
<div id="legend">{$trans.webloads} | <span id="brown">{$trans.webplays}</span> | <span id="grey">{$trans.feedloads}</span></div>
<!-- <div style="width: 2px; height: 156px; left: 0px; background-color: blue;"></div> -->

{foreach from=$postings item=posting}
<div class="column" style="left: {$posting.x}px; height: 2px; background-color: gray; cursor: pointer;" onclick="location.href='index.php?page=record2&amp;auth={$record2_auth_key}&amp;do=edit&amp;id={$posting.id}';"></div>
<div class="column" style="left: {$posting.x}px; height: {$posting.h}px; cursor: pointer;" onclick="location.href='index.php?page=record2&amp;auth={$record2_auth_key}&amp;do=edit&amp;id={$posting.id}';">
<div class="cweb" style="height: {$posting.hweb}px;"></div>
<div class="cfla" style="height: {$posting.hfla}px; bottom: {$posting.yfla}px;"></div>
<div class="cpod" style="height: {$posting.hpod}px; bottom: {$posting.ypod}px;"></div>
<!-- <div class="cdate"><span>{$posting.cdate}</span></div> -->   
<div class="title"><span>{$posting.ctitle}<br />{$posting.cdate}<br />{$posting.countall} downloads</span></div>
</div>
{/foreach}
</div>
</table>
       
</div><!-- close content  -->

{include file='manager_footer.tpl'}
