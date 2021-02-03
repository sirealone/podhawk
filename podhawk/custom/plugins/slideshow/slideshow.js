	function selectItem(li) { }
	function formatItem(row) {return row;}
	$(document).ready(function() {
	setUpAutoComplete($('.suggest'));
	});

	$(document).ready(function() {
	$('.remove').click(function(event){
		event.preventDefault();
		var theRow = $(this).parent().parent();
		var imageName = theRow.children(0).find('input').attr('value');
		if (imageName === '') {imageName = 'this row';}
		var confirm = yesno("Are you sure you want to remove " + imageName + "?");
		if (confirm == true)  {
			if ($('.image_rows').length > 1) {
				theRow.hide('slow', function(){theRow.remove()});}
			else {
				theRow.find('input').val('');
				theRow.find('textarea').empty();
				theRow.find('textarea').val('');
				}
			}				
		})
	});

	$(document).ready(function() {
	$('#addRow').click(function(event) {
		event.preventDefault();
		$('.image_rows:last').clone(true).fadeIn('slow').insertAfter('.image_rows:last');
		$('.image_rows:last').find('input').val('');
		$('.image_rows:last').find('textarea').empty();
		var suggest = $('.image_rows:last').find('input.suggest');
		setUpAutoComplete(suggest);
        return false;
		})
	});

	function setUpAutoComplete(elements) {
		$(elements).autocomplete('index.php?page=autocomplete&type=images',
		 {
			minChars:1,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:selectItem,
			formatItem:formatItem,
			selectOnly:1,
			mode:"single" })
		};

	function showThumbsOptions() {
		var toShow = $('.textlink');
		var toHide = $('.thumblink');
		var thumbStatus = $('#thumbs_radio_buttons > td:eq(1) > input[type=radio]:nth(0)').is(':checked');
		if (thumbStatus == true) {
			toShow.show('slow');
			toHide.hide('slow');
		} else {
			toShow.hide('slow');
			toHide.show('slow');
		}
	}

	$(document).ready(function() {
		showThumbsOptions();
		$('#thumbs_radio_buttons').find('input[name=thumbs]').change(function(){showThumbsOptions()});
		});

	$(document).ready(function() {
		$('#slideshow_form').submit(function() {
			if ($('input[name=name]').attr('value') == '') {
			alert ('You must give your slideshow a name.');
			return false;
				}
			})
		});

	$(document).ready(function() {
		$('#runSlideshow').on('click', function(event) {
			$('.newSlideshow').remove();
			var auth = $('#slideshow_form > input:nth(0)').val();
			$("tr[class='image_rows']").each(function() {
			var theImage = $(this).find('input').val();
			var theCaption = $(this).find('textarea').val();
			if (theImage != '') {
				if (theImage.substring(0,7) == 'http://' || theImage.substring(0,8) == 'https://')
				{
					var href= 'index.php?page=slideshow&action=getexternalimage&url=' + encodeURIComponent(theImage) +'&auth=' + auth;
				}
				else
				{
					var href = '../images/' + theImage;
				}
				$(document.body).append('<a class="newSlideshow" href="' + href + '" rel="lightbox[backendPageSlideshow]" title="' + theCaption + '"></a>');
				}			
			})
			$('.newSlideshow:nth(0)').click();
			event.preventDefault();	
		})		
	});

