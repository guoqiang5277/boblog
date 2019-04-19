<?php
global $plugin_ubbeditor_functions, $plugin_ubbeditor_buttons;
$plugin_ubbeditor_buttons.=<<<eot
<a href="JavaScript: void(0); "><img border="0" onclick="addfmp()" title="" src="plugin/fmplayer/fmp.gif" ></a>
eot;
$plugin_ubbeditor_functions.=<<<eot
function addfmp() {
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[fmp]" + range.text + "[/fmp]";
	} else if (is_firefox && noweditorid.selectionEnd) {
		txt=FxGetTxt ("[fmp]", "[/fmp]");
		return;
	} else {
		txt=prompt(jslang[59],"http://");
		if(txt!=null) {
			AddTxt="[fmp]"+txt;
			AddText(AddTxt);
			AddTxt="[/fmp]";
			AddText(AddTxt);
		}
	}
}
eot;
?>