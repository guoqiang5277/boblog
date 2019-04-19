var tinyMCE = null, tinyMCELang = null;

function TinyMCEPopup() {
};

TinyMCEPopup.prototype.init = function() {
	var win = window.opener ? window.opener : window.dialogArguments;
	var inst;

	if (!win) {
		// Try parent
		win = parent.parent;

		// Try top
		if (typeof(win.tinyMCE) == "undefined")
			win = top;
	}

	window.opener = win;
	this.windowOpener = win;
	this.onLoadEval = "";

	// Setup parent references
	tinyMCE = win.tinyMCE;
	tinyMCELang = win.tinyMCELang;

	if (!tinyMCE) {
		alert("tinyMCE object reference not found from popup.");
		return;
	}

	inst = tinyMCE.selectedInstance;
	this.isWindow = tinyMCE.getWindowArg('mce_inside_iframe', false) == false;
	this.storeSelection = (tinyMCE.isMSIE && !tinyMCE.isOpera) && !this.isWindow && tinyMCE.getWindowArg('mce_store_selection', true);

	if (this.isWindow)
		window.focus();

	// Store selection
	if (this.storeSelection)
		inst.selectionBookmark = inst.selection.getBookmark(true);

	// Setup dir
	if (tinyMCELang['lang_dir'])
		document.dir = tinyMCELang['lang_dir'];

	// Setup title
	var re = new RegExp('{|\\\$|}', 'g');
	var title = document.title.replace(re, "");
	if (typeof tinyMCELang[title] != "undefined") {
		var divElm = document.createElement("div");
		divElm.innerHTML = tinyMCELang[title];
		document.title = divElm.innerHTML;

		if (tinyMCE.setWindowTitle != null)
			tinyMCE.setWindowTitle(window, divElm.innerHTML);
	}

	// Output Popup CSS class
	document.write('<link href="' + tinyMCE.getParam("popups_css") + '" rel="stylesheet" type="text/css">');

	tinyMCE.addEvent(window, "load", this.onLoad);
};

TinyMCEPopup.prototype.onLoad = function() {
	var body = document.body;

	if (tinyMCE.getWindowArg('mce_replacevariables', true))
		body.innerHTML = tinyMCE.applyTemplate(body.innerHTML, tinyMCE.windowArgs);

	var dir = tinyMCE.selectedInstance.settings['directionality'];
	if (dir == "rtl") {
		var elms = document.forms[0].elements;
		for (var i=0; i<elms.length; i++) {
			if ((elms[i].type == "text" || elms[i].type == "textarea") && elms[i].getAttribute("dir") != "ltr")
				elms[i].dir = dir;
		}
	}

	if (body.style.display == 'none')
		body.style.display = 'block';

	// Execute real onload (Opera fix)
	if (tinyMCEPopup.onLoadEval != "") {
		eval(tinyMCEPopup.onLoadEval);
	}
};

TinyMCEPopup.prototype.executeOnLoad = function(str) {
	if (tinyMCE.isOpera)
		this.onLoadEval = str;
	else
		eval(str);
};

TinyMCEPopup.prototype.resizeToInnerSize = function() {
	// Netscape 7.1 workaround
	if (this.isWindow && tinyMCE.isNS71) {
		window.resizeBy(0, 10);
		return;
	}

	if (this.isWindow) {
		var doc = document;
		var body = doc.body;
		var oldMargin, wrapper, iframe, nodes, dx, dy;

		if (body.style.display == 'none')
			body.style.display = 'block';

		// Remove margin
		oldMargin = body.style.margin;
		body.style.margin = '0';

		// Create wrapper
		wrapper = doc.createElement("div");
		wrapper.id = 'mcBodyWrapper';
		wrapper.style.display = 'none';
		wrapper.style.margin = '0';

		// Wrap body elements
		nodes = doc.body.childNodes;
		for (var i=nodes.length-1; i>=0; i--) {
			if (wrapper.hasChildNodes())
				wrapper.insertBefore(nodes[i].cloneNode(true), wrapper.firstChild);
			else
				wrapper.appendChild(nodes[i].cloneNode(true));

			nodes[i].parentNode.removeChild(nodes[i]);
		}

		// Add wrapper
		doc.body.appendChild(wrapper);

		// Create iframe
		iframe = document.createElement("iframe");
		iframe.id = "mcWinIframe";
		iframe.src = document.location.href.toLowerCase().indexOf('https') == -1 ? "about:blank" : tinyMCE.settings['default_document'];
		iframe.width = "100%";
		iframe.height = "100%";
		iframe.style.margin = '0';

		// Add iframe
		doc.body.appendChild(iframe);

		// Measure iframe
		iframe = document.getElementById('mcWinIframe');
		dx = tinyMCE.getWindowArg('mce_width') - iframe.clientWidth;
		dy = tinyMCE.getWindowArg('mce_height') - iframe.clientHeight;

		// Resize window
		// tinyMCE.debug(tinyMCE.getWindowArg('mce_width') + "," + tinyMCE.getWindowArg('mce_height') + " - " + dx + "," + dy);
		window.resizeBy(dx, dy);

		// Hide iframe and show wrapper
		body.style.margin = oldMargin;
		iframe.style.display = 'none';
		wrapper.style.display = 'block';
	}
};

TinyMCEPopup.prototype.resizeToContent = function() {
	var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	var isOpera = (navigator.userAgent.indexOf("Opera") != -1);

	if (isOpera)
		return;

	if (isMSIE) {
		try { window.resizeTo(10, 10); } catch (e) {}

		var elm = document.body;
		var width = elm.offsetWidth;
		var height = elm.offsetHeight;
		var dx = (elm.scrollWidth - width) + 4;
		var dy = elm.scrollHeight - height;

		try { window.resizeBy(dx, dy); } catch (e) {}
	} else {
		window.scrollBy(1000, 1000);
		if (window.scrollX > 0 || window.scrollY > 0) {
			window.resizeBy(window.innerWidth * 2, window.innerHeight * 2);
			window.sizeToContent();
			window.scrollTo(0, 0);
			var x = parseInt(screen.width / 2.0) - (window.outerWidth / 2.0);
			var y = parseInt(screen.height / 2.0) - (window.outerHeight / 2.0);
			window.moveTo(x, y);
		}
	}
};

TinyMCEPopup.prototype.getWindowArg = function(name, default_value) {
	return tinyMCE.getWindowArg(name, default_value);
};

TinyMCEPopup.prototype.restoreSelection = function() {
	if (this.storeSelection) {
		var inst = tinyMCE.selectedInstance;

		inst.getWin().focus();

		if (inst.selectionBookmark)
			inst.selection.moveToBookmark(inst.selectionBookmark);
	}
};

TinyMCEPopup.prototype.execCommand = function(command, user_interface, value) {
	var inst = tinyMCE.selectedInstance;

	this.restoreSelection();
	inst.execCommand(command, user_interface, value);

	// Store selection
	if (this.storeSelection)
		inst.selectionBookmark = inst.selection.getBookmark(true);
};

TinyMCEPopup.prototype.close = function() {
	tinyMCE.closeWindow(window);
};

TinyMCEPopup.prototype.pickColor = function(e, element_id) {
	tinyMCE.selectedInstance.execCommand('mceColorPicker', true, {
		element_id : element_id,
		document : document,
		window : window,
		store_selection : false
	});
};

TinyMCEPopup.prototype.openBrowser = function(element_id, type, option) {
	var cb = tinyMCE.getParam(option, tinyMCE.getParam("file_browser_callback"));
	var url = document.getElementById(element_id).value;

	tinyMCE.setWindowArg("window", window);
	tinyMCE.setWindowArg("document", document);

	// Call to external callback
	if (eval('typeof(tinyMCEPopup.windowOpener.' + cb + ')') == "undefined")
		alert("Callback function: " + cb + " could not be found.");
	else
		eval("tinyMCEPopup.windowOpener." + cb + "(element_id, url, type, window);");
};

// Setup global instance
var tinyMCEPopup = new TinyMCEPopup();

tinyMCEPopup.init();

var currentpicname;
var realpath="attachment/";

function insertmyUpload (filename) {
	filename=filename.replace(/\*/g, '.');
	filename=realpath+filename;
	if (document.getElementById('ifautoaddubb').checked) filename=autoattachubb (filename);
	tinyMCE.execCommand('mceInsertContent', false, filename);
	tinyMCEPopup.close();
}

function generateUpload (attachid, filename) {
	filename=filename.replace(/\*/g, '.');
	filename=realpath+filename;
	if (document.getElementById('ifautoaddubb').checked) filename=autoattachubb (attachid, filename);
	tinyMCE.execCommand('mceInsertContent', false, filename);
	tinyMCEPopup.close();
}

function autoattachubb (attachid, filename) {
	var finalresult;
	finalresult="[file]"+attachid+"[/file]";
	var extindex = filename.lastIndexOf(".");
	if (extindex!=-1) {
		var realext=filename.substring(extindex+1).toLowerCase();
		if (realext=="gif" || realext=="jpg" || realext=="png" || realext=="bmp" || realext=="jpeg") {
			var antileechurl;
			antileechurl=attachid.replace(/\[attach\]/g, '');
			antileechurl=antileechurl.replace(/\[\/attach\]/g, '');
			antileechurl="attachment.php?fid="+antileechurl;
			 finalresult="<img src='"+antileechurl+"' alt='' border='0' class='insertimage' />";
		}
		else if (realext=="swf")  {
			 finalresult="[swf=400,300]"+attachid+"[/swf]";
		}
		else if (realext=="wma" || realext=="mp3" || realext=="asf" || realext=="wmv")  {
			 finalresult="[wmp=400,300]"+attachid+"[/wmp]";
		}
		else if (realext=="rm" || realext=="rmvb" || realext=="ra" || realext=="ram")  {
			 finalresult="[real=400,300]"+attachid+"[/real]";
		}
		else if (realext=="htm" || realext=="html")  {
			 finalresult="[url]"+attachid+"[/url]";
		}
		else if (realext=="zip" || realext=="rar")  {
			 finalresult="[file]"+attachid+"[/file]";
		}
	}
	return finalresult;
}

function picPreview (filename, attachid) {
	var divdvs="<div style='margin-top: 0px;  width: 400px !important; width: 320px; height: 165px; background-repeat: no-repeat; background-position: 130px 20px !important; background-position: 0px 20px; background-image: url("+filename+");'>"+jslang[18]+"<input type='text' id='picalign' value='m' size=1 maxlength=1>(l-"+jslang[19]+",m-"+jslang[20]+",r-"+jslang[21]+") <input type='button' value='"+jslang[22]+"' onclick=\"doinsertimage('"+attachid+"');\"></div>";
	document.getElementById('picp').innerHTML=divdvs;
}

function doinsertimage(attachid) {
	var antileechurl;
	antileechurl=attachid.replace(/\[attach\]/g, '');
	antileechurl=antileechurl.replace(/\[\/attach\]/g, '');
	antileechurl="attachment.php?fid="+antileechurl;
	var alignpro=document.getElementById('picalign').value;
	var plusalign;
	if (alignpro=='l') plusalign=' align="left"';
	else if  (alignpro=='r') plusalign=' align="right"';
	else plusalign='';
	var finalresult="<img"+plusalign+" alt='' border='0' src='"+antileechurl+"' />";
	tinyMCE.execCommand('mceInsertContent', false, finalresult);
	tinyMCEPopup.close();
}
