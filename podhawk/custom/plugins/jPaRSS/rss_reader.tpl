{*  RSS Reader *}

{* parameters

id = unique id of ul element
feedURL = URL of feed
number = number of items (defaults to 4)
show_date = 'true' or 'false' - whether or not to show date of each feed item
description = 'false' (no description), 'true' (truncated description), 'content' (full description), 'image' (image plus truncated description)

NB the rss_reader plugin must be enabled for this template to work!

*}

<script type="text/javascript">
$(document).ready(function(){ldelim}
    $("#{$id}").PaRSS(
	"{$feedURL}",
	{$number|default:4},
	{if $show_date == 'true'}'{$plugins_page_elements.date_format_php}'{else}''{/if},
	{if $description == 'content' || $description == 'image'}'{$description}',{else}{$description|default:'false'},{/if}
	function(){ldelim}{rdelim} 
    );
  });
</script>
<ul id="{$id}">
<li>Loading feed.....</li>
</ul>

