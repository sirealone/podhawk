function saythis(request) 
{ 
    alert(request);
} 

function yesno(request) 
{ 
    if (confirm(request)) 
    { 
        return true; 
    } 
    else 
    { 
        return false; 
    } 
} 
    
function link_popup(src,xsize,ysize) 
{
    var atts = "top=15, left=15, resize=0, location=0, scrollbars=0, statusbar=0, menubar=0, width="+ xsize + ", height=" + ysize;
    var theWindow = window.open(src.getAttribute('href'), 'popup', atts);
    theWindow.focus();
    return theWindow;
}

function active_popup(src,name,xsize,ysize) 
{
    var atts = "top=15, left=15, resize=1, location=0, scrollbars=0, statusbar=0, menubar=0, width="+ xsize + ", height=" + ysize;
    var theWindow = window.open(src, name, atts);
    theWindow.focus();
}

 function  checkImageResizeData()
 {
    var theForm = document.formResize;
    var ratio = theForm.inputRatio.value;
    var width = theForm.newWidth.value;
    var newName = theForm.newname.value;
    var newHeight;
    newHeight = Math.round(ratio * width);
    if (isNaN(width) == true || width == "" || width == 0)
    {
      alert("Please enter a number in the width input box");
      theForm.newWidth.focus();
      return false;
  }
  else {
    if (validImageName(newName) == false) {
      alert("Invalid image name!\n\nAllowed characters are upper and lower case letters,\nnumbers, hyphens and underscores.");
      theForm.newname.focus();
      return false;
    } else {
    if (confirm("Resizing will give an image with these dimensions:\n\nWidth " + width + " px\nHeight " + newHeight + " px\n\nName of resized image : " + newName))
    {
    return true;
   } else {
     return false;
   }
  }
 }
  }
  
  function validImageName(name)
  {
    var theRegEx = /[^a-zA-Z0-9_-]/;
   return !(theRegEx.test(name));
  }
  
  function newNameValid ()
  {
    var theForm =   document.rename;
    var newName = theForm.newimagename.value;
    if (validImageName(newName) == false || newName == "")
    {
      alert("Invalid image name!\n\nAllowed characters are upper and lower case letters,\nnumbers, hyphens and underscores.");
      theForm.newimagename.focus();
      return false;
    }
    else {
      return true;
    }
  }
  

function checkAlt ()  {
  var theForm = document.taginfo;
  var alt = theForm.alt.value;
  if (alt == "") {
    alert('Valid image tags must contain an "alt" attribute.\nPlease complete the "alt" field.');
    theForm.alt.focus();
    return false;
  } else {
    return true;
  }
}

function showEmffPlayer()  {
	var emffSelect = document.getElementById("emffselect").selectedIndex;
	var emffBackground = document.getElementById("emffbackground").value;
	var emffStandard = document.getElementById("emffstandard").checked;
	var emffValues =  new Array();
	emffValues = emffData(emffSelect);
	var emffName = emffValues[0];
	var emffHeight = emffValues[1];
	var emffWidth = emffValues[2];
	if (emffStandard == false) {
		var background = '<param name="bgcolor" value="' + emffBackground + '" />';
		}
	else  {
		var background = "";
		}
	
	var emffCode = '<object type="application/x-shockwave-flash" data="custom/players/emff/emff_' + emffName + '.swf?src=backend/test.mp3" width="' + emffWidth +'" height="' + emffHeight + '"><param name="movie" value="custom/players/emff/emff_' + emffName + '.swf" />' + background + '<param name="FlashVars" value="src=backend/test.mp3" /></object>';
	
	var thePlayer = document.getElementById("showemff");
	thePlayer.innerHTML = emffCode;

	
	}

function emffData(index)  {
	switch (index)  {

		case 0:
		var name = 'easy_glaze';
		var height = 32;
		var width = 32;
		break;
		case 1:
		var name = 'easy_glaze_small';
		var height = 22;
		var width = 22;
		break;
		case 9:
		var name = 'standard';
		var height = 34;
		var width = 110;
		break;
		case 7:
		var name = 'silk';
		var height = 32;
		var width = 84;
		break;
		case 4:
		var name = 'old';
		var height = 55;
		var width = 120;
		break;
		case 6:
		var name = 'position_blue';
		var height = 50;
		var width = 100;
		break;
		case 2:
		var name = 'lila';
		var height = 55;
		var width = 200;
		break;
		case 3:
		var name = 'lila_info';
		var height = 55;
		var width = 200;
		break;
		case 11:
		var name = 'wooden';
		var height = 60;
		var width = 120;
		break;
		case 10:
		var name = 'stuttgart';
		var height = 30;
		var width = 140;
		break;
		case 5:
		var name = 'old_noborder';
		var height = 25;
		var width = 91;
		break;
		case 8:
		var name = 'silk_button';
		var height = 16;
		var width = 16;
		break;		
		default:
		var name = 'anotherplayer';
		var height = 0;
		var width = 0;
		break;
		}
	var data = new Array(name, height, width);
	
	return data;
}

function showStreamerData()  {
		
		var link1 = document.getElementById("jwlink1").checked;
		var link2 = document.getElementById("jwlink2").checked;
		var link3 = document.getElementById("jwlink3").checked;
		var link4 = document.getElementById("jwlink4").checked;
		

		if (link1 ||link2||link3||link4) {
		document.getElementById("streamerdata").className = 'shown';
		}
		else {
		document.getElementById("streamerdata").className = 'hidden';
		}
}

function showPixeloutPlayer()   {
	
	var theTable = document.getElementById("pix_data");
	var inputs = new Array();
	inputs = theTable.getElementsByTagName("input");
	var pixData = new Array();
	for (var x = 0; x < inputs.length; x++)  {
		pixData[x] = inputs[x].value;				
		}
	AudioPlayer.setup("custom/players/onepixelout/player.swf", {width: pixData[0], initialvolume: 100, transparentpagebg: "yes",bg: pixData[1],leftbg: pixData[2],lefticon: pixData[3],voltrack: pixData[13], volslider: pixData[14], rightbg: pixData[4], rightbghover: pixData[5], righticon: pixData[6], righticonhover: pixData[7],loader: pixData[12],track: pixData[10],tracker: pixData[9],border: pixData[11],skip: pixData[15],text: pixData[8],animation: "no"});

	AudioPlayer.embed("pixelout_player", {soundFile: "backend/test.mp3"});
}

function showJwPlayer ()   {

	var audio_width = document.getElementById("jw_audio_width").value;
	var audio_height = document.getElementById("jw_audio_height").value;
	var video_width = document.getElementById("jw_video_width").value;
	var video_height = document.getElementById("jw_video_height").value;
	var backcolor = document.getElementById("jw_backcolor").value;
	var frontcolor = document.getElementById("jw_frontcolor").value;
	var lightcolor = document.getElementById("jw_lightcolor").value;
	var screencolor = document.getElementById("jw_screencolor").value;
	var playlistsize = document.getElementById("jw_playlistsize").value;

	var controlbarElement = document.getElementById("jw_controlbar");
	var controlbar = getSelectValue(controlbarElement);
	
	var playlistElement = document.getElementById("jw_playlist");
	var playlist = getSelectValue(playlistElement);

	var skinElement = document.getElementById("jw_skin");
	var skin = getSelectValue(skinElement);
	if (skin == 'default')
	{
		skin = '';
	}
	else if (skin.slice(-4) == '.swf')
	{
		skin = "custom/players/jwplayer/skins/"+skin;	
	}
	else
	{
		skin = "custom/players/jwplayer/skins/" + skin + "/" + skin + ".zip";
	}
	var useSkinColours = ($('input[name=jw_use_skin_colours]:nth(0)').is(':checked') == true);
	var resizingElement = document.getElementById("jw_resizing");
	var resizing = getSelectValue(resizingElement);

	var stretchingElement = document.getElementById("jw_stretching");
	var stretching = getSelectValue(stretchingElement);

	var iconsElement = document.getElementById("jw_icons");
	var icons = getSelectValue(iconsElement);

	var playIndex;
	for (playIndex = 0; playIndex < 3; playIndex++)  {
		if (document.form1.filetoplay[playIndex].checked == true)
		break;
		}

	var height;
	var width;
	var file;
	if (playIndex == 0) {
		height = audio_height;
		width = audio_width;
		file = "backend/test.mp3";
		icons = false;
		playlist = 'none';
		}
	if (playIndex == 1)  {
		height = video_height;
		width = video_width;
		file = "http://content.longtailvideo.com/videos/flvplayer.flv";
		playlist = 'none';
		}
	if (playIndex == 2)  {
		if (playlist == 'bottom')  {
			height = parseInt(video_height) + parseInt(playlistsize);
			width = video_width;
			}
		else if (playlist == 'right')  {
			height = video_height;
			width = parseInt(video_width) + parseInt(playlistsize);
			}
		else  {
			height = video_height;
			width = video_width;
			}
		file = "http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured";
		}

	if (useSkinColours == false)
	{
			jwplayer('jw_player').setup({
				'height' : height,
				'width' : width,
				'flashplayer' : 'custom/players/jwplayer/player.swf',
				'file': file,
				'backcolor': backcolor,
				'frontcolor': frontcolor,
				'lightcolor': lightcolor,
				'screencolor': screencolor,
				'playlist.position' : playlist,
				'playlist.size': playlistsize,
				'controlbar.position': controlbar,
				'skin': skin,
				'resizing': resizing,
				'stretching': stretching,
				'icons': icons 
				});
	} else {
				jwplayer('jw_player').setup({
				'height' : height,
				'width' : width,
				'flashplayer' : 'custom/players/jwplayer/player.swf',
				'file': file,
				'playlist.position' : playlist,
				'playlist.size': playlistsize,
				'controlbar.position': controlbar,
				'skin': skin,
				'resizing': resizing,
				'stretching': stretching,
				'icons': icons 
				});
	}					
}

function getSelectValue (select)  {

	var options = new Array();
	options = select.getElementsByTagName("option");
	var index = select.selectedIndex;
	var value = options[index].value;
	return value;
	
}

function showTemplateLanguageOptions ()  {

	var select = document.getElementById("template");
	var chosenTemplate = getSelectValue(select);
	
	var options = new Array();
	options = select.getElementsByTagName("option");
	
	for (var x = 0; x < options.length; x++)  {
		var template = options[x].value;		
		var theTable = document.getElementById(template + "_langs");

			if (theTable != null) {
				if (template == chosenTemplate)  {
				theTable.className = "shown";
				}  else  {
				theTable.className = "hidden";
				}
			}
		}	
		
}

function toggleThis(button, elementId)  {
	var theElement = document.getElementById(elementId);
	if (button == 0)  {
		theElement.className = "shown";
		}  else  {
		theElement.className = "hidden";
		}
	}

function selectAllText(id) {

    document.getElementById(id).focus();
    document.getElementById(id).select();
}

function testFTPData(auth) {
	var server = document.getElementById("ftp_server").value;
	var user = document.getElementById("ftp_user").value;
	var password = document.getElementById("ftp_pass").value;
	var path = document.getElementById("ftp_path").value;

	$('#ftp_test_result').empty();
	$('#ftp_test_result').append("Trying to connect to server...");

	$.post('index.php?page=testFTP',
		{'auth': auth, 'server': server, 'user': user, 'password': password, 'path': path},
		function(data) {
			var html;
			if (data.length > 500) {
				html = "Sorry - there has been a problem. Perhaps you are no longer logged in.";
			}  else  {
				html = data;
			}
			$('#ftp_test_result').empty();			
			$('#ftp_test_result').append(html);
			});

	}

function autosave(id, authcode, editor)
{

	if (autoSaveEnabled == true)
	{
		t = setTimeout("autosave(" + id +", '" + authcode + "'," + editor + ")", 20000);

		if (firstSaveComplete == false)
		{
			firstSaveComplete = true;
			$('#message').append(" Autosave is on. <a href=\"javascript: toggleAutosave("+id +", '"+authcode+"',"+editor+");\">Turn autosave off.</a>");
			return;
		}
	
		var title = $("#title").val();
		var summary = $("#summary").val();

		if (editor == 4)
		{
			var ed = tinyMCE.get('txtarea');
			var content = ed.getContent();
		}
		else
		{
			var content = $("#txtarea").val();
		}

		if (title.length > 0 || content.length > 0 || summary.length > 0) 
		{ 
		    $.post("index.php?page=autosave",
		    { 	'id' : id,
				'title' : encodeURIComponent(title),
				'content' : encodeURIComponent(content),
				'summary' : encodeURIComponent(summary),
				'auth' : authcode,
				'editor' : editor
			},
		    function(data)

			{		
				if (data.success === true)
				{
					var now = new Date();
					var hours = now.getHours();
					if (hours < 10)
					{
						hours = "0" + hours;
					}

					var minutes = now.getMinutes();
					if (minutes < 10)
					{
						minutes = "0" + minutes;
					}

					var seconds = now.getSeconds();
					if (seconds < 10)
					{
						seconds = "0" + seconds;
					}

					var message = data.html + hours + ":" + minutes + ":" + seconds;
					message = message + ". Autosave is on. <a href=\"javascript: toggleAutosave(" + id +", '" + authcode + "'," + editor + ");\">Turn autosave off.</a>"; 
				}
				else
				{
					var message = data.html;
				}

				$("#message").empty();
				$("#message").append(message + '.');
									        
		    },
			"json"); 
		}
	}
	else
	{
		$('#message').empty();
		$('#message').append("Autosave is off. <a href=\"javascript: toggleAutosave(" + id +", '" + authcode + "'," + editor + ");\">Turn autosave on.</a>");
	} 
}

function toggleAutosave(id, authcode, editor)
{
	if (autoSaveEnabled == true)
	{
		autoSaveEnabled = false;
	}
	else
	{
		autoSaveEnabled = true;
	}
	clearTimeout (t);
	autosave (id, authcode, editor);
}

function showRowsFromCheckbox(checkboxName) {
	var toShow = $('tr.' + checkboxName);

	if ($('input[name=' + checkboxName + ']').is(':checked'))
	{
		toShow.show();
	}
	else
	{
		toShow.hide();
	}

	$('input[name=' + checkboxName + ']').click(function() {
		toShow.toggle();
		});
	}

function showRowsFromTwoCheckboxes(firstCheckboxName, secondCheckboxName) {
	var toShow = $('tr.' + firstCheckboxName);
	var firstInput = $('input[name=' + firstCheckboxName + ']');
	var secondInput = $('input[name=' + secondCheckboxName + ']');
	if (firstInput.is(':checked') && secondInput.is(':checked'))
	{
		toShow.show();
	}
	else
	{
		toShow.hide();
	}
	firstInput.click(function() {		
		showRowsFromTwoCheckboxes(firstCheckboxName, secondCheckboxName);
	});
	secondInput.click(function() {		
		showRowsFromTwoCheckboxes(firstCheckboxName, secondCheckboxName);
	});
	}	

function showRowsFromRadioButton(buttonName) {
	var toShow = $('tr.' + buttonName);
	var toHide = $('tr.no_' + buttonName);
	if ($('input[name=' + buttonName + ']:nth(0)').is(':checked') == true)
	{
		toShow.show();
		toHide.hide();
	}
	else
	{
		toShow.hide();
		toHide.show();
	}

	$('input[name=' + buttonName + ']').change(function() {
		toShow.toggle();
		toHide.toggle();
	});

}

function showElementsFromSelectBox(selectBoxName) {
	var selectedValue = $(':input[name=' + selectBoxName +'] option:selected').val();
	$('[id^=' + selectBoxName + ']').hide();
	$('#' + selectBoxName + '_' + selectedValue).show();

	$(':input[name=' + selectBoxName + ']').change(function(){
		var newSelectedValue = $(':input[name=' + selectBoxName +'] option:selected').val();
		$('[id^=' + selectBoxName + ']').hide();
		$('#' + selectBoxName + '_' + newSelectedValue).show();
	});
	
}

function showCommentTextEditorDiv()
{
	var selectedCommentSystem = $(":input[name='acceptcomments'] option:selected").val();
	if (selectedCommentSystem == 'loudblog' || selectedCommentSystem == 'akismet')
	{
		$('#comment_text_editor').show();
	}
	else
	{
		$('#comment_text_editor').hide();
	}
	$(':input[name=acceptcomments]').change(function(){
	var newSelectedCommentSystem = $(':input[name=acceptcomments] option:selected').val();
	if (newSelectedCommentSystem == 'loudblog' || newSelectedCommentSystem == 'akismet')
	{
		$('#comment_text_editor').show();
	}
	else
	{
		$('#comment_text_editor').hide();
	}
	});
}


function menuShowHide() {
	
	$('.menuItem > ul > li > a').each(function(){
		$(this).mouseenter(function(){
			$(this).css('color','#E31A27');
		});
		$(this).mouseleave(function(){
			$(this).css('color', '#4C4CA5');
		});
	});
	$('.menuItem').each(function() {
		$(this).children('h3').mouseenter(function(){
			$(this).css('cursor', 'pointer');
			$(this).css('color','#E31A27');
			});
		$(this).children('h3').mouseleave(function(){
			$(this).css('cursor', 'default');
			$(this).css('color', '#4C4CA5');
			});
		$(this).children('h3').click(function() {
			$(this).siblings('ul').toggle('normal');
			$(this).parent().siblings('div').children('ul').hide('normal');
		});
	});
}

function showAmazonMessage(direction) {
	var amazon_message;
	if (direction == 'up') amazon_message = 'Uploading the file to Amazon S3. This may take a few minutes ...';
	if (direction == 'down') amazon_message = 'Downloading the file to the audio directory. This may take a few minutes ...';	
	$('#message').empty();
	$('#message2').empty();
	$('#message').append(amazon_message);
	sleep(5);
	}
	
