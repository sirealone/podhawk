{*  places list of recent postings in sidebar  *}

{* --------SOME USER DEFINED OPTIONS ----------------- *}

{* to display posting tags after the posting title, change false to true below *}
{assign var="with_tags" value=false}

{* to display posting summary in a paragraph below the posting title, change false to true below *}
{assign var="with_summary" value=false}

{* to display a thumbnail image with the posting summary, change false to true below *}
{assign var="with_image" value=false}

{*to set the height and width of the thumbnail, edit the values below (in pixels) *}
{assign var="thumbnail_width" value=50}
{assign var="thumbnail_height" value=50}

{* use CSS to style span.recent_postings_tag, p.recent_postings_summary and img.recent_postings_image as needed *}

{* -------------- END -------------------- *}

{last_postings cat=$cat alpha=$alpha number=$number date_range=$date_range}

{if $last_postings|@count > 0}
<ul>
	{foreach from=$last_postings item=posting name=last_postings_loop}
		{if isset($smarty.get.month)}
		{assign var="posting_month" value=$posting.posted|date_format:"%Y-%m"}
		{/if}
		{if !isset($smarty.get.month) || $smarty.get.month == $posting_month} 	
		<li><a href="{$posting.permalink}" title="{$trans.link_posting}">{$posting.title}</a>{if $with_date == true || $with_author == true}<br /><span class="postinfo">{if $with_date == true} <span class="postdate">{$posting.posted|date_format:$settings.preferred_date_format}</span>{/if}{if $with_author == true} by {$posting.author}{/if}</span>{/if}
		{if $with_tags == true}
		{foreach from=$posting.tag_array item=tag} <a href="{$posting.tag_links.$tag}" title="{$trans.link_tags} {$tag|replace:'_':''}"><span class="recent_postings_tag">{$tag|replace:'_':''}</span></a>{/foreach}
		{/if}
		
	{if $with_summary == true && !empty($posting.summary)}
	<p class="recent_posting_summary">
		{if $with_image == true && !empty($posting.image)}
		<img class="recent_posting_image" alt="posting_image" src="podhawk/timthumb/timthumb.php?src={$posting.image|escape:'url'}&w={$thumbnail_width}&h={$thumbnail_height}&zc=1" />
		{/if}
	{$posting.summary}</p>
	{/if}
	</li>
	{/if}	
	{/foreach}
</ul>
{/if}
