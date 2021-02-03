function hide_widgits()  {

	var widgits = document.getElementById('widgits');
	var widgit_items = new Array();
	widgit_items = widgits.getElementsByTagName('li');
	for (var x = 0; x < size(widgit_items); x++) {
	alert(x);
	}
}
		 

