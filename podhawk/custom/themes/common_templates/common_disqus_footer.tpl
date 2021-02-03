{*  the bit at the bottom of the page which enables disqus to display the number of comments for each post  *}

{if $settings.acceptcomments == 'disqus'}

	<script type="text/javascript">
	//<![CDATA[
	(function() {ldelim}
		var links = document.getElementsByTagName('a');
		var query = '?';
		for(var i = 0; i < links.length; i++) {ldelim}
			if(links[i].href.indexOf('#disqus_thread') >= 0) {ldelim}
				query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
			{rdelim}
		{rdelim}
		document.write('<script charset="utf-8" type="text/javascript" src="http://disqus.com/forums/{$settings.disqus_name}/get_num_replies.js' + query + '"></' + 'script>');
	{rdelim})();
	//]]>
	</script>

{/if}

