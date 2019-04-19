function getMultimediaInsert() {
	var mm_type=document.getElementById('mm_type').options[document.getElementById('mm_type').selectedIndex].value;
	var mm_src=document.getElementById('mm_src').value;
	var mm_width=document.getElementById('mm_width').value;
	var mm_height=document.getElementById('mm_height').value;
	insertMultimedia(mm_type, mm_src, mm_width, mm_height);
}

function insertMultimedia(mm_type, mm_src, mm_width, mm_height) {
	if (!mm_src || mm_src==null || mm_src=='') {
		alert ("No Source File!");
		return;
	}
	var html = '['+mm_type+'='+mm_width+','+mm_height+']'+mm_src+'[/'+mm_type+']';

	tinyMCE.execCommand('mceInsertContent', false, html);
	tinyMCEPopup.close();
}
