{*    template for settings page   *}
{include file='manager_head.tpl'}

<body id="settings"{*  onload="showInitialCommentOptions()"*}>

<div id="wrapper"{if $warning == true} class="warning"{/if}>
	<div id="header">
	<h1>{$trans.create}</h1>
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">
{if !empty($message)}
<p class="msg">{$trans.$message|default:$message}</p><br />
{elseif !empty($update_message)}
<p class="msg">{$update_message}</p><br />
{/if}

{if $admin == true}

<form action="index.php?page=settings&amp;do=save" method="post" enctype="multipart/form-data" id="settings_form">
<input type ="hidden" name="auth" value="{$settings_auth_key}" />
<div id="meta" class="unit">
<h2>{$trans.sec_meta}</h2>
<table>
{*   language   *}
<tr>
    <td class="left">{$trans.language}</td>
    <td class="center">
        <select name="language">
        {foreach from=$languages item='language'}
	<option value="{$language}" {if $language == $settings.language}selected="selected"{/if}>{$language}</option>
	{/foreach}
        </select>    
    </td>
    <td class="right">{$trans.languagehelp}</td>
</tr>


{*   site name   *}
<tr>
    <td class="left">{$trans.name}</td>
    <td class="center">
    <input name="sitename" type="text"
    value="{$settings.sitename}" />
    </td>
    <td class="right">
    {$trans.namehelp}
    </td>
</tr>

{*   site slogan   *}
<tr>
    <td class="left">{$trans.slogan}</td>
    <td class="center">
    <input name="slogan" type="text"
    value="{$settings.slogan}" />
    </td>
    <td class="right">
    {$trans.sloganhelp}
    </td>
</tr>

{*   site description   *}
<tr>
    <td class="left">{$trans.desc}</td>
    <td class="center">
    <textarea name="description" rows="4">{$settings.description}</textarea>
    </td>
    <td class="right">
    {$trans.deschelp}
    </td>
</tr>

{*   site url   *}
<tr>
    <td class="left">{$trans.url}</td>
    <td class="center">
    <input name="url" type="text"
    value="{$settings.url}" />
    </td>
    <td class="right">
    {$trans.urlhelp}
    </td>
</tr>

{*  caching   *}
<tr>
    <td class="left">{$trans.caching}</td>
    <td class="center">
    <input class="radio" name="caching" type="radio" value="1" {if $settings.caching == true} checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="caching" type="radio" value="0" {if $settings.caching == false} checked="checked"{/if} />{$trans.no}
    </td>
    <td class="right">{$trans.caching_help}</td>
</tr>

{*   error reporting   *}
<tr>
	<td class="left">{$trans.error_reporting}</td>
	<td class="center">
	<input class="radio" name="error_reporting" type="radio" value="0" {if $settings.error_reporting == 0}checked="checked"{/if} />{$trans.error_none}<br />
	<input class="radio" name="error_reporting" type="radio" value="1" {if $settings.error_reporting == 1}checked="checked"{/if} />{$trans.error_some}<br />
	<input class="radio" name="error_reporting" type="radio" value="2" {if $settings.error_reporting == 2}checked="checked"{/if} />{$trans.error_all}<br />
	</td>
	<td class="right">{$trans.error_reporting_help}</td>
</tr>

</table>

<table>
<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>

</div>

<div id="webpage" class="unit">
<h2>{$trans.sec_webpage}</h2>
<table>
{*   theme   *}
<tr>
    <td class="left">{$trans.theme}</td>
    <td class="center">
    <select name="template" id="template" onchange = "showTemplateLanguageOptions();">
    {foreach from=$themes item=theme}
        <option value="{$theme}"
        {if $theme == $settings.template} selected="selected"{/if}>{$theme}</option>
    {/foreach}
    </select>
    </td>
    <td class="right">
    {$trans.themehelp}
    </td>
</tr>
</table>



{*   language options for each theme   *}

{foreach from=$themes item=theme}
	{if isset($theme_langs.$theme)}
	<div id="{$theme}_langs" class="hidden">
	<table>
	<tr><td class="left">{$theme|capitalize} {$trans.lang_options}. {$trans.theme_lang}</td>
	<td class="center">
		<select name="{$theme}_template_language" id="{$theme}_template_language"}>	
		{foreach from=$theme_langs.$theme item='lang'}
		<option value="{$lang}"{if $lang == $settings.template_language} selected="selected"{/if}>{$lang}</option>
		{/foreach}
		</select>
	</td>
	<td class="right">
	{$trans.theme_lang_help}
	</td></tr>
	</table>
	</div>
	{/if}
{/foreach}	

<table>
{*  postings per web page    *}
<tr>
    <td class="left">{$trans.posts_per_page}</td>
    <td class="center"><input name="posts_per_page" type="text" value="{$settings.posts_per_page}" /></td>
    <td class="right">{$trans.posts_per_page_help}</td>
</tr>

{*   homepage    *}

<tr>
	<td class="left">{$trans.homepage}</td>
	<td class="center">
	<select name="homepage">
	<option value = '0'{if $settings.homepage==0} selected="selected"{/if}>All categories</option>
	{foreach from=$categories item=category}
	<option value = '{$category.id}'{if $settings.homepage == $category.id} selected = "selected"{/if}>{$category.name}</option>
	{/foreach}
	</select>
	</td>
	<td class="right">
	{$trans.homepage_help}
	</td>
</tr>

{*   number of hyperlinks   *}
<tr>
    <td class="left">{$trans.hyperlinks}:</td>
    <td class="center">
    <input name="showlinks" type="text"
    value="{$settings.showlinks}" />
    </td>
    <td class="right">
    {$trans.hyperlinkshelp}
    </td>
</tr>

{*  preferred date format   *}
{assign var='dummy' value='2009-04-20 09:18:00'}
<tr>
	<td class="left">{$trans.date_format}</td>
	<td class="center">
	<select id="date_format" name="preferred_date_format">
	<option value="%b %e, %Y" {if $settings.preferred_date_format == '%b %e, %Y'}selected="selected"{/if}>{$dummy|date_format:'%b %e, %Y'}</option>
	<option value="%e %b %Y" {if $settings.preferred_date_format == '%e %b %Y'}selected="selected"{/if}>{$dummy|date_format:'%e %b %Y'}</option>
	<option value="%B %e, %Y" {if $settings.preferred_date_format == '%B %e, %Y'}selected="selected"{/if}>{$dummy|date_format:'%B %e, %Y'}</option>
	<option value="%e %B %Y" {if $settings.preferred_date_format == '%e %B %Y'}selected="selected"{/if}>{$dummy|date_format:'%e %B %Y'}</option>	
	<option value="%e %b %y" {if $settings.preferred_date_format == '%e %b %y'}selected="selected"{/if}>{$dummy|date_format:'%e %b %y'}</option>
	<option value="%d/%m/%y" {if $settings.preferred_date_format == '%d/%m/%y'}selected="selected"{/if}>{$dummy|date_format:'%d/%m/%y'}</option>
	<option value="%m/%d/%y" {if $settings.preferred_date_format == '%m/%d/%y'}selected="selected"{/if}>{$dummy|date_format:'%m/%d/%y'}</option>
	<option value="%d-%m-%Y" {if $settings.preferred_date_format == '%d-%m-%Y'}selected="selected"{/if}>{$dummy|date_format:'%d-%m-%Y'}</option>
	<option value="%Y-%m-%d" {if $settings.preferred_date_format == '%Y-%m-%d'}selected="selected"{/if}>{$dummy|date_format:'%Y-%m-%d'}</option>	
	</select>
	</td>
	<td class="right">{$trans.date_format_help}</td>
</tr>

{*   download counting   *}
<tr>
    <td class="left">{$trans.count}</td>
    <td class="center">
    <input class="radio" name="countweb" type="checkbox" value="1" {if $settings.countweb == true}checked="checked"{/if} />{$trans.countweb}<br />
    <input class="radio" name="countfla" type="checkbox" value="1" {if $settings.countfla == true}checked="checked"{/if} />{$trans.countfla}<br />
    <input class="radio" name="countpod" type="checkbox" value="1" {if $settings.countpod == true}checked="checked"{/if} />{$trans.countpod}
    </td>
    <td class="right">
    {$trans.counthelp}
    </td>
</tr>

{*   count visitors   *}
<tr>
    <td class="left">{$trans.visitors}</td>
    <td class="center">
    <input class="radio" name="count_visitors" type="radio" value="1" {if $settings.count_visitors == true} checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="count_visitors" type="radio" value="0" {if $settings.count_visitors == false} checked="checked"{/if} />{$trans.no}
    </td>
    <td class="right">{$trans.visitors_help}</td>
</tr>

</table>


	
<table>
<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>

</div>

<div id="feed" class="unit">
<h2>{$trans.sec_feed}</h2>
<table>

{*   rss feed author   *}
<tr>
    <td class="left">{$trans.feedauthor}</td>
    <td class="center">
    <input name="itunes_author" type="text"
    value="{$settings.itunes_author}" />
    </td>
    <td class="right">
    {$trans.feedauthorhelp}
    </td>
</tr>

{*   rss feed e-mail   *}
<tr>
    <td class="left">{$trans.feedmail}</td>
    <td class="center">
    <input name="itunes_email" type="text"
    value="{$settings.itunes_email}" />
    </td>
    <td class="right">
    {$trans.feedmailhelp}
    </td>
</tr>

{*   rss feed copyright   *}
<tr>
    <td class="left">{$trans.copyright}</td>
    <td class="center">
    <input name="copyright" type="text"
    value="{$settings.copyright}" />
    </td>
    <td class="right">
    {$trans.copyrighthelp}
    </td>
</tr>

{*   rss feed number of items   *}
<tr>
    <td class="left">{$trans.items}</td>
    <td class="center">
    <input name="rss_postings" type="text"
    value="{$settings.rss_postings}" />
    </td>
    <td class="right">
    {$trans.itemshelp}
    </td>
</tr>

{*   i-tunes art   *}
<tr>
    <td class="left">{$trans.itunesart}:</td>
    <td class="center">
    <input class="fileupper" type="file" name="itunes_image" accept="image/*" />
    </td>
    <td class="right">
    <a href="../images/itunescover.jpg"><img class="coverart" src="../images/itunescover.jpg" /></a>{$trans.itunesarthelp}
    </td>
</tr>

{*   rss image   *}
<tr>
    <td class="left">{$trans.rssimage}:</td>
    <td class="center">
    <input class="fileupper" type="file" name="feedimage" accept="image/*" />
    </td>
    <td class="right">
    <a href="../images/rssimage.jpg"><img class="rssimage" src="../images/rssimage.jpg" /></a>{$trans.rssimagehelp}
    </td>
</tr>

<tr>
    <td class="left">{$trans.explicit}</td>
    <td class="center">
    <input class="radio" name="itunes_explicit" type="radio" value="1"{if $settings.itunes_explicit == true} checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="itunes_explicit" type="radio" value="0"{if $settings.itunes_explicit == false} checked="checked"{/if} />{$trans.no}
    </td>
    <td class="right">
    {$trans.explicithelp}
    </td>
</tr>

{*   itunes categories   *}

<tr>
    <td class="left">{$trans.itunescats}:</td>
    <td class="center">
 

        <select class="itcats" name="feedcat1">	
	{foreach from=$itunescats key=short item=long}     
           <option value="{$short}"
            {if $settings.feedcat1 == $short} selected="selected"{/if}>{$long}</option>
	{/foreach}          
 	</select>
        
        <select class="itcats" name="feedcat2">
	{foreach from=$itunescats key=short item=long} 	     
           <option value="{$short}"
            {if $settings.feedcat2 == $short} selected="selected"{/if}>{$long}</option>
	{/foreach}          
 	</select>

        <select class="itcats" name="feedcat3">
	{foreach from=$itunescats key=short item=long} 	     
           <option value="{$short}"
            {if $settings.feedcat3 == $short} selected="selected"{/if}>{$long}</option>
	{/foreach}          
 	</select>

	<select class="itcats" name="feedcat4">
	{foreach from=$itunescats key=short item=long} 	     
           <option value="{$short}"
            {if $settings.feedcat4 == $short} selected="selected"{/if}>{$long}</option>
	{/foreach}          
 	</select>

    </td>
    <td class="right">
    {$trans.itunescatshelp}
    </td>
</tr>

{*   itunes language   *}
<tr>
    <td class="left">{$trans.langloc}:</td>
    <td class="center">
        <select name="languagecode">
	{foreach from=$itunes_languages key=long item=short}
	<option value="{$short}" {if $settings.languagecode == $short} selected="selected"{/if}>{$long}</option>
	{/foreach}
	</select>
          
    </td>
    <td class="right">
    {$trans.langlochelp}
    </td>
</tr>

{*   alternate feed address eg FeedBurner    *}
<tr>
	<td class="left">{$trans.alt_feed}</td>
	<td class="center">
    	<input name="alternate_feed_address" type="text" value="{if $settings.alternate_feed_address == ''}http://{else}{$settings.alternate_feed_address}{/if}" /></td>
	<td class="right">{$trans.alt_feed_help}</td>
</tr>

<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>
</div>

<div id="comments" class="unit">
<h2>{$trans.sec_comments}</h2>
<table>

<tr>
{*  which commenting system do we want to use  *}
	<td class="left">{$trans.comment_system}</td>
	<td class="center">
	<select id="comment_choice" name="acceptcomments" >
		<option value="none" {if $settings.acceptcomments == 'none'}selected="selected"{/if}>{$trans.no_comments}</option>
		<option value="loudblog" {if $settings.acceptcomments == 'loudblog'}selected="selected"{/if}>{$trans.loudblog_comments}</option>
		<option value="akismet" {if $settings.acceptcomments == 'akismet'}selected="selected"{/if}>{$trans.akismet_comments}</option>
		<option value="disqus" {if $settings.acceptcomments == 'disqus'}selected="selected"{/if}>{$trans.disqus_comments}</option>
	</select>
	
</td><td class="right">{$trans.comments_help}</td></tr>
</table>

<div id="acceptcomments_loudblog">
<table>
<tr><td class="left">{$trans.spamquestion}:</td>
    <td class="center">
    	<input name="spamquestion" type="text" value="{$settings.spamquestion}" />
    </td>
    <td class="right">{$trans.choose_spam_question} {$trans.spamquestionhelp}
    </td>
</tr>

<tr>
    <td class="left">{$trans.spamanswer}:</td>
    <td class="center">
    	<input name="spamanswer" type="text" value="{$settings.spamanswer}" />
    </td>
    <td class="right">
    {$trans.spamanswerhelp}
    </td>
</tr>

</table>
</div>

<div id="acceptcomments_akismet">
<table>

<tr>
    <td class="left">{$trans.akismet_key}:</td>
    <td class="center">
    	<input name="akismet_key" type="text" value="{$settings.akismet_key}" />
    </td>
    <td class="right">
    {$trans.akismet_help}
    </td>
</tr>

<tr>
    <td class="left">{$trans.keep_spam}</td>
    <td class="center">
    <input class="radio" name="keep_spam" type="radio" value="1" {if $settings.keep_spam == true}checked="checked" {/if}/>{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="keep_spam" type="radio" value="0" {if $settings.keep_spam == false}checked="checked" {/if}"/>{$trans.no}
    </td>
    <td class="right">
    {$trans.keep_spam_help}
    </td>
</tr>

</table>
</div>

<div id="comment_text_editor">
<table>
</tr>
	<td class="left">{$trans.comment_text_editor}</td>
	<td class="center">
	<input class="radio" name="comment_text_editor" type="radio" value="1" {if $settings.comment_text_editor == true}checked="checked"{/if}/>{$trans.yes}&nbsp;&nbsp;
	<input class="radio" name="comment_text_editor" type="radio" value="0" {if $settings.comment_text_editor == false}checked="checked"{/if}/>{$trans.no}
	</td>
	<td class="right">
	{$trans.comment_text_editor_help}
	</td>
</tr>
</table>
</div>

<div id="acceptcomments_disqus">
<table>

<tr><td class="left">{$trans.short_name}</td>
<td class="center">
    	<input name="disqus_name" type="text" value="{$settings.disqus_name}" />
    </td>
    <td class="right">
    {$trans.disqus_help}
    </td>
</tr>
</table>
</div>

<table>
<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td class="right"><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>
</div>

<div id="backend" class="unit">

<h2>{$trans.sec_backend}</h2>
<table>

{*   html helper   *}
{* 	1 = Textile with normal tags + object and embed
	2 = Markdown - deprecated, default to 6
	3 = BBCode - deprecated, default to 6
	4 = TinyMCE with normal tags
	5 = Very Simple Editor (p and a tags only)
	6 = Roll my own HTML
*}
<tr>
    <td class="left">{$trans.html}</td>
    <td class="center">   
    <input class="radio" name="markuphelp" type="radio" value="1" {if $settings.markuphelp == 1}checked="checked" {/if}/>Textile<br />
    <input class="radio" name="markuphelp" type="radio" value="4" {if $settings.markuphelp == 4}checked="checked"{ /if}/>Tiny MCE <br />
	<input class="radio" name="markuphelp" type="radio" value="5" {if $settings.markuphelp == 5}checked="checked"{ /if}/>{$trans.simple}<br />
	<input class="radio" name="markuphelp" type="radio" value="6" {if $settings.markuphelp == 6}checked="checked"{/if}/>{$trans.raw}
    </td>
    <td class="right">
   	{$trans.htmlhelp}
    </td>
</tr>

{*  postings per admin page   *}
<tr>
    <td class="left">{$trans.showpostings}</td>
    <td class="center"><input name="showpostings" type="text" value="{$settings.showpostings}" /></td>
    <td class="right">{$trans.showpostings_help}</td>
</tr>

{* Autosave recording page 2 *}
<tr>
	<td class="left">{$trans.autosave}</td>
	<td class="center">
		<input class="radio" name="autosave" type="radio" value="1" {if $settings.autosave == true} checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    	<input class="radio" name="autosave" type="radio" value="0" {if $settings.autosave == false} checked="checked"{/if} />{$trans.no}
	</td>
	<td class="right">{$trans.autosave_help}</td>
</tr>

{* Use Amazon S3 to store audio files *}
<tr>
	<td class="left">{$trans.amazon_1}</td>	
	<td class="center">
		<input class="radio" name="amazon" type="radio" value="1" {if $settings.amazon == true} checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    	<input class="radio" name="amazon" type="radio" value="0" {if $settings.amazon == false} checked="checked"{/if} />{$trans.no}
	</td>
	<td class="right">{$trans.amazon_2}</td>
</tr>

<tr class="amazon">
	<td class="left">{$trans.amazon_3}</td>
	<td class="center">
		<input name="amazon_access" type="text" value="{$settings.amazon_access}" />
	</td>
	<td class="right"></td>
</tr>

<tr class="amazon">
	<td class="left">{$trans.amazon_4}</td>
	<td class="center">
		<input name="amazon_secret" type="text" value="{$settings.amazon_secret}" />
	</td>
	<td class="right"></td>
</tr>

<tr class="amazon">
	<td class="left">{$trans.amazon_5}</td>
	<td class="center">
		<input name="amazon_bucket" type="text" value="{$settings.amazon_bucket}" />
	</td>
	<td class="right">{$trans.amazon_6}</td>
</tr>	
		

<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>
</div>

{*   renaming audio files   *}
<div id="filename" class="unit">
<h2>{$trans.sec_filename}</h2>
<table>
<tr>
    <td class="left">{$trans.rename}</td>
    <td class="center">    
    <input class="radio" name="rename" type="radio" value="1" {if $settings.rename == 1}checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="rename" type="radio" value="0" {if $settings.rename == 0}checked="checked"{/if} />{$trans.no}
    </td>
    <td class="right">
    {$trans.renamehelp}
    </td>
</tr>

<tr class="rename">
    <td class="left">{$trans.custfile}:</td>
    <td class="center">
    <input name="filename" type="text"
    value="{$settings.filename}" />
    </td>
    <td class="right">
    {$trans.custfilehelp}
    </td>
</tr>

<tr class="rename">
    <td class="left">{$trans.filedemo}:</td>
    <td class="center">
    <code>{$settings.filename}-2005-05-27-51816.mp3 
    </code>
    </td>
    <td class="right">
    {$trans.filedemohelp}
    </td>
</tr>

<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>
</div>

{*  rewriting id3 tags   *}
<div id="id3" class="unit">
<h2>{$trans.sec_id3}</h2>
<table>
<tr>
    <td class="left">{$trans.id3write}</td>
    <td class="center">    
    <input class="radio" name="id3_overwrite" type="radio" value="1" {if $settings.id3_overwrite == true}checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="id3_overwrite" type="radio" value="0" {if $settings.id3_overwrite == false}checked="checked"{/if} />{$trans.no} 
    </td>
    <td class="right">
    {$trans.id3writehelp}
    </td>
</tr>

<tr class="id3_overwrite">
    <td class="left">{$trans.id3album}:</td>
    <td class="center">
    <input name="id3_album" type="text"
    value="{$settings.id3_album}" />
    </td>
    <td class="right">
    {$trans.id3albumhelp}
    </td>
</tr>

<tr class="id3_overwrite">
    <td class="left">{$trans.id3artist}:</td>
    <td class="center">
    <input name="id3_artist" type="text"
    value="{$settings.id3_artist}" />
    </td>
    <td class="right">
    {$trans.id3artisthelp}
   </td>
</tr>

<tr class="id3_overwrite">
    <td class="left">{$trans.id3genre}:</td>
    <td class="center">
    <input name="id3_genre" type="text"
    value="{$settings.id3_genre}" />
    </td>
    <td class="right">
    {$trans.id3genrehelp}
    </td>
</tr>

<tr class="id3_overwrite">
    <td class="left">{$trans.id3comment}:</td>
    <td class="center">
    <textarea name="id3_comment" rows="4">{$settings.id3_comment}</textarea>
    </td>
    <td class="right">
    {$trans.id3commenthelp}    
    </td>
</tr>

<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>
</table>

</div>

{*  ftp settings  *}

<div id="ftp" class="unit">
<h2>{$trans.sec_ftp}</h2>
<table>
<tr>
    <td class="left">{$trans.useftp}</td>
    <td class="center">    
    <input class="radio" name="ftp" type="radio" value="1" {if $settings.ftp == true}checked="checked"{/if} />{$trans.yes}&nbsp;&nbsp;
    <input class="radio" name="ftp" type="radio" value="0" {if $settings.ftp == false}checked="checked"{/if} />{$trans.no}
    </td>
    <td class="right">
    {$trans.useftphelp}
    </td>
</tr>
{if $ftp_extension_loaded == true}
<tr>
	<td colspan = "3"><b>{$trans.ftp_layer}</b></td>
</tr>  
{/if}
</table>

<div id="ftp_data">
<table>



<tr>
    <td class="left">{$trans.ftpserver}:</td>
    <td class="center">
    <input name="ftp_server" id="ftp_server" type="text"
    value="{$settings.ftp_server}" />
    </td>
    <td class="right">
    {$trans.ftpserverhelp}
    </td>
</tr>

<tr>
    <td class="left">{$trans.ftpuser}:</td>
    <td class="center">
    <input name="ftp_user" id="ftp_user" type="text"
    value="{$settings.ftp_user}" />
    </td>
    <td class="right">
    {$trans.ftpuserhelp}
    </td>
</tr>

<tr>
    <td class="left">{$trans.ftppass}:</td>
    <td class="center">
    <input name="ftp_pass" id="ftp_pass" type="text"
    value="{$settings.ftp_pass}" />
    </td>
    <td class="right">
    {$trans.ftppasshelp}
    </td>
</tr>

<tr>
    <td class="left">{$trans.ftppath}:</td>
    <td class="center">
    <input name="ftp_path" id="ftp_path" type="text"
    value="{$settings.ftp_path}" />
    </td>
    <td class="right">
    {$trans.ftppathhelp}
    </td>
</tr>

{if $ftp_extension_loaded == true}
<tr>
	<td class="left"></td>
	<td class="center"><button type="button" id="ftp_test_button" onclick="var auth='{$testFTP_auth_key}'; testFTPData(auth);">{$trans.ftp_button}</button></td>
	<td class="right" id="ftp_test_result">{$trans.ftp_button_help}</td>
</tr>
{/if}
</table>
</div>

<table>

<tr><td colspan="2"><a href="#header">{$trans.return}</a></td>
<td><input type="submit" value="{$trans.saveset}" /></td>
</tr>

</table>
</div>


</form>

{/if}  {*   close 'admin only' condition  *}
</div><!--   close content   -->

{include file='manager_footer.tpl'}

