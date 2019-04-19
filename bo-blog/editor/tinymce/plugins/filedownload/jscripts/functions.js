function getFileInsert() {
	var mm_type=(document.getElementById('registeronly').checked) ? 'sfile' : 'file';
	var mm_src=document.getElementById('filesrc').value;
	insertFile(mm_type, mm_src);
}

function insertFile(mm_type, mm_src) {
	if (!mm_src || mm_src==null || mm_src=='') {
		alert ("No Source File!");
		return;
	}
	var html = '['+mm_type+']'+mm_src+'[/'+mm_type+']';

	tinyMCE.execCommand('mceInsertContent', false, html);
	tinyMCEPopup.close();
}
