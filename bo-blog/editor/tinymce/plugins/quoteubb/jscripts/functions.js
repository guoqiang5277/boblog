function init() {
	var anySelection = false;
	var txtorigin = tinyMCE.getWindowArg('anysel');

	if (txtorigin) {
		if (txtorigin!=null) {
			document.getElementById('mm_txt').value=txtorigin;
		}
	}
}

function getInsert() {
	var mm_type=document.getElementById('mm_type').options[document.getElementById('mm_type').selectedIndex].value;
	var mm_txt=document.getElementById('mm_txt').value;
	insertQuoteUBB(mm_type, mm_txt);
}

function insertQuoteUBB(mm_type, mm_txt) {
	var html = '['+mm_type+']'+mm_txt+'[/'+mm_type+']';
	tinyMCE.execCommand('mceReplaceContent', false, html);
	tinyMCEPopup.close();
}
