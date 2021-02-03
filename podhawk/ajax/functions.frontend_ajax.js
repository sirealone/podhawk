// moves the calendar back or forwards by one month
function updateCalendar(month, gets)
{
	var updateUrl = "podhawk/ajax/updatecalendar.php?cal=" + month;

	$.each(gets, function(key, value) {
		if (value != '')
		{
			updateUrl += "&" + key + "=" + encodeURIComponent(value);
		}
	});

	$.getJSON(updateUrl, function(data)
	{
		$('#calendar').empty();
		$('#calendar').append(data.html);
	});
}

// toggles the display of a bar code for downloading podcasts to mobile phones
function display_qr(url, link, id)
{
	var googleAPICall = "http://chart.apis.google.com/chart?cht=qr&chl=" + url + "/" + link + "&chs=200x200";
	if (document.getElementById('qr' + id).childNodes.length == 0)
	{
		$('#qr'+id).append('<br /><img src="' + googleAPICall + '" alt="bar code" />');
	}
	else
	{
		$('#qr'+id).empty();
	}
}
