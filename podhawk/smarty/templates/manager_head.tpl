<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <meta http-equiv="content-language" content="en" />
    <title>{$sitename} - {$page}</title>

    <meta name="robots" content="noindex, nofollow" />
    <meta name="description" content="PodHawk" />

    <meta name="author" content="Peter Carter, Birmingham, UK" />

	    
    <link rel="stylesheet" type="text/css" href="smarty/templates/manager.css" />
	<link rel="stylesheet" type="text/css" href="backend/autocomplete.css" />
    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="podhawk/backend/ie.css"  />
    <![endif]-->

	{foreach from=$plugins_css item=css}
		{$css}
	{/foreach}

	<script src="{$jquery_location}" type="text/javascript"></script>
    <script src="backend/functions.js" type="text/javascript"></script>
    <script src="backend/autocomplete.js" type="text/javascript"></script>

{* javascript for menu *}
{if $page != "login"}
	<script type="text/javascript">
	$(document).ready(function() {ldelim}	
	menuShowHide();
	{rdelim});
	</script>
{/if}

{if $page == "login" and $use_encrypted_handshake == true}
	<script src="backend/md5.js" type="text/javascript"></script>
	<script type="text/javascript">
	function doCHAP(){ldelim}
  	var valid=true;
	var usrid=document.getElementById('login_name');  	
  	var psw=document.getElementById('password');  	
  	var chlng=document.getElementById('challenge');  	
  	chlng.value=hex_md5(hex_md5(psw.value)+'{$challenge}');
  	psw.value='';	
  	return valid;
	{rdelim}	
</script>
{/if}

{if $page == "record1"}
	<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" type="text/javascript"></script>
	<script src="uploadify/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
	<script src="uploadify/uploadify_functions.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="uploadify/uploadify.css" />
	<script type="text/javascript">
	$(document).ready(function() {ldelim}
        $('#flashupload').uploadify({ldelim}
			'uploader': 'uploadify/uploadify.swf',
			'script':    'uploadify/uploadify.php',
			'scriptData': {ldelim}'auth' : '{$sessid}', 'upload_type' : 'audio'{rdelim},
			'method' : 'GET',
			'folder':    '../upload',
			'multi':  'true',
			'cancelImg': 'uploadify/cancel.png',
			'wmode': 'transparent',
			'scriptAccess': 'always',
			'onAllComplete': function(e,d) {ldelim} updateUploadFolder(e, d); {rdelim}        	
		{rdelim});
	{rdelim});
	$(document).ready(function() {ldelim}
	$('#fromUpload').bind('submit', function(event) {ldelim}
		var s = $("select[name='filename'] option:selected").val();
		if (s === '')
		{ldelim}
			$('#upload_message').empty();
			$('#upload_message').append('<p class="msg">Please select a file.</p>');
			event.preventDefault();
		{rdelim}
	{rdelim});
	{rdelim});					
	</script>
{/if}

{if $page == "images"}
	<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" type="text/javascript"></script>
	<script src="uploadify/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="uploadify/uploadify.css" />
	<script type="text/javascript">
{literal}
	$(document).ready(function()
	{
		var uploadedFiles = new Array();
        $('#flashupload').uploadify
		({
			'uploader': 'uploadify/uploadify.swf',
			'script':    'uploadify/uploadify.php',
			'scriptData': {'auth' : '{/literal}{$sessid}{literal}', 'upload_type' : 'image'},
			'method' : 'GET',
			'folder':    '../images',
			'multi':  'true',
			'cancelImg': 'uploadify/cancel.png',
			'wmode': 'transparent',
			'scriptAccess': 'always',
			'onComplete' : function(e,q,f,r,d)
							{
								uploadedFiles.push(f.name);
								return true;
							},
			'onAllComplete': function(e,d)
							{
							var data = uploadedFiles.join(" ");
							data = encodeURIComponent(data);
							window.location = 'index.php?page=images&action=list&auth={/literal}{$images_auth_key}{literal}&choose_3=' + data;
							}        	
		});
	});
{/literal}
	</script>
	{if $ipage == 'list'}
	<script type="text/javascript" src="lib/lightbox/lightbox.js"></script>
	<link rel="stylesheet" type="text/css" href="lib/lightbox/lightbox.css" media="screen" />
	{/if}
	{if $ipage == 'make_html'}
		<script type="text/javascript">
		$(document).ready(function(){ldelim}
		showRowsFromRadioButton('lightbox');
		{rdelim});
		</script>
	{/if}
{/if}

{if $page == "players"}
		<script src="backend/jscolor/jscolor.js" type="text/javascript"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
		<script type="text/javascript" src="custom/players/onepixelout/audio-player-uncompressed.js"></script>
		<script type="text/javascript" src="custom/players/jwplayer/jwplayer.js"></script>
		
		<script type="text/javascript">
		$(document).ready(function(){ldelim}
		showElementsFromSelectBox('audio_player_type');
		showPixeloutPlayer();
	{if $jw_player_installed == true}
			if ($("#jw_video_width") != null) {ldelim}
				showJwPlayer(true);
				{rdelim}
	{/if}
		{rdelim});
		</script>
{/if}

{if $page == "record2"}

	{* autocomplete tags *}
	<script type="text/javascript">
	{literal}
	function selectItem(li) {}
	function formatItem(row) {return row;}
	$(document).ready(function() {
		$("#suggest").autocomplete('index.php?page=autocomplete&type=tags', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1, mode:"multiple",multipleSeparator:" " });
	});
	{/literal}
	</script>

	{* tiny mce *}
	{if $editor_to_use == 4}
		<script src="tiny_mce/tiny_mce.js" type="text/javascript"></script>

		<script type="text/javascript">
		tinyMCE.init({ldelim}
		mode : "specific_textareas",
		editor_selector: "ed",
		theme : "advanced",
		plugins : "safari,paste,insertdatetime,advhr,autosave, fullscreen",
		theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,charmap,|,insertdate,inserttime,|,cleanup,help,code",
		theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,blockquote,|,advhr,|,link,unlink,|,forecolor,backcolor, |, fullscreen",
		theme_advanced_buttons3 : "undo,redo,removeformat,|,formatselect,fontselect,fontsizeselect,|,image",	
		theme_advanced_toolbar_location : "bottom",		
		convert_fonts_to_spans : false,
		relative_urls : false,
		remove_script_host : false,
		document_base_url : "{$url}/",
		external_image_list_url : "{$url}/podhawk/index.php?page=imagelist&auth={$imagelist_auth_key}",
		plugin_insertdate_dateFormat : "%a %d %b %Y",
		plugin_insertdate_timeFormat : "%H:%M"

		{rdelim});
		</script>
	{/if}

	{* autosave posting text, title and summary *}
	{if $autosave == true && $may_edit == true}
		<script type="text/javascript">
		var firstSaveComplete = false;
		var autoSaveEnabled = {$autosave_temp};
		var t; 	
		$(document).ready(function(){ldelim}
		autosave({$id}, '{$autosave_auth_key}', {$editor_to_use}); 
		{rdelim});
		</script>
	{/if}

	{* jw player *}	 
	{if $posting.playertype == "jwvideo"}		
		<script type="text/javascript" src="custom/players/jwplayer/jwplayer.js"></script>		
	{/if}

	<script type="text/javascript">	
	$(document).ready(function() {ldelim}
		$('#addfiles').click(function() {ldelim}
		window.open("index.php?page=addfiles&id={$posting.id}","addfiles","width=700,height=500,scrollbars=yes");
			{rdelim})
		{rdelim});	
	</script>	

{/if}

{if $page == 'settings'}
	<script type="text/javascript">
	$(document).ready(function(){ldelim}
	showRowsFromRadioButton('id3_overwrite');
	showRowsFromRadioButton('rename');
	showRowsFromRadioButton('amazon');
	showElementsFromSelectBox('acceptcomments');
	showTemplateLanguageOptions();
	showCommentTextEditorDiv();
	{rdelim});
	</script>
{/if}

{if $page == 'id3'}
	{if !empty($posting_title)}
	<script type="text/javascript">
	$(document).ready(function() {ldelim}
		$('#use_title').bind('click', function() {ldelim}
			$('#id3title').val('{$posting_title}');		
			{rdelim});
		$('#use_title').mouseover(function() {ldelim}
			$(this).css('cursor', 'pointer');
			{rdelim});
		{rdelim});
	</script>
	{/if}

	{if !empty($posting_summary)}
	<script type="text/javascript">
	$(document).ready(function() {ldelim}
		$('#use_summary').bind('click', function() {ldelim}
			$('#id3comment').empty();
			$('#id3comment').append('{$posting_summary}');
			{rdelim});
		$('#use_summary').mouseover(function() {ldelim}
			$(this).css('cursor', 'pointer');
			{rdelim});
		{rdelim});
	</script>
	{/if}
	{literal}<script type="text/javascript">
	function selectItem(li) {
	}

	function formatItem(row) {
		return row;
	}

	$(document).ready(function() {
		$("#suggest").autocomplete('index.php?page=autocomplete&type=images', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1, mode:"multiple",multipleSeparator:" " });
	});
	</script>{/literal}
{/if}

{if $page == 'authors1'}
	<script type="text/javascript">
	function validateNewAuthorForm()
	{ldelim}
		var x=document.forms["newAuthor"]["newnick"].value;
		if (x==null || x=="")
  		{ldelim}
		var msg = 'You must give your new author a nickname';
		$('#message').empty();
		$('#message').append('You must give your new author a nickname');
		$('#newnick').focus();
		return false;
  		{rdelim}
	{rdelim}
	</script>
{/if}

{if $page == 'comments' && $smarty.get.subpage == 'edit'}
	<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="tiny_mce/comments_editor.js"></script>
{/if}

{if $page == 'addfiles'}
	{literal}
	<script type="text/javascript" src="lib/lightbox/lightbox.js"></script>
	<link rel="stylesheet" type="text/css" href="lib/lightbox/lightbox.css" media="screen" />
	<script type="text/javascript">
	$(document).ready(function() {
		$('#closeButton').click(function() {
			window.close();
			})
		});
	$(document).ready(function() {
		$('#suggest').focus(function() {
			$(this).val('');
			})
		});

	function selectItem(li) {
	}
	function formatItem(row) {
		return row;
	}
	$(document).ready(function() {
		$("#suggest").autocomplete('index.php?page=autocomplete&type=images', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1, mode:"single"});
	});
	</script>
	{/literal}
{/if}

{foreach from=$plugins_head_script item=script}
	{$script}
{/foreach}
</head>
