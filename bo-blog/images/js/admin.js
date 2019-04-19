<!-- Move Up and Down JS from:  Bob Rockers (brockers@subdimension.com) [javascript.internet.com] -->
function move(fbox,tbox) {
var i = 0;
if(fbox.value != "") {
var no = new Option();
no.value = fbox.value;
no.text = fbox.value;
tbox.options[tbox.options.length] = no;
fbox.value = "";
   }
}
function remove(box) {
for(var i=0; i<box.options.length; i++) {
if(box.options[i].selected && box.options[i] != "") {
box.options[i].value = "";
box.options[i].text = "";
   }
}
BumpUp(box);
} 
function BumpUp(abox) {
for(var i = 0; i < abox.options.length; i++) {
if(abox.options[i].value == "")  {
for(var j = i; j < abox.options.length - 1; j++)  {
abox.options[j].value = abox.options[j + 1].value;
abox.options[j].text = abox.options[j + 1].text;
}
var ln = i;
break;
   }
}
if(ln < abox.options.length)  {
abox.options.length -= 1;
BumpUp(abox);
   }
}
function Moveup(dbox) {
for(var i = 0; i < dbox.options.length; i++) {
if (dbox.options[i].selected && dbox.options[i] != "" && dbox.options[i] != dbox.options[0]) {
var tmpval = dbox.options[i].value;
var tmpval2 = dbox.options[i].text;
dbox.options[i].value = dbox.options[i - 1].value;
dbox.options[i].text = dbox.options[i - 1].text
dbox.options[i-1].value = tmpval;
dbox.options[i-1].text = tmpval2;
dbox.options[i-1].selected='selected'; //Improved by Bob
dbox.options[i].selected=''; //Improved by Bob
      }
   }
}
function Movedown(ebox) {
for(var i = 0; i < ebox.options.length; i++) {
if (ebox.options[i].selected && ebox.options[i] != "" && ebox.options[i+1] != ebox.options[ebox.options.length]) {
var tmpval = ebox.options[i].value;
var tmpval2 = ebox.options[i].text;
ebox.options[i].value = ebox.options[i+1].value;
ebox.options[i].text = ebox.options[i+1].text
ebox.options[i+1].value = tmpval;
ebox.options[i+1].text = tmpval2;
ebox.options[i+1].selected='selected'; //Improved by Bob
ebox.options[i].selected=''; //Improved by Bob
break; //Improved by Bob
      }
   }
}
<!-- End Move Up and Down JS -->

function GetOptions(ebox, urlnew) {
	var optionsout='';
	for(var i = 0; i < ebox.options.length; i++) {
		optionsout+=ebox.options[i].value+':';
	}
	var urlnews=urlnew+optionsout;
	if (shutajax==0) {
		makeRequest(urlnews+"&ajax=on", 'adminSubmitAjaxRun', 'GET', null);
	}
	else {
		window.location=urlnews;
	}
}

function checkallbox(the_form, do_check) {
    var elts = (typeof(document.forms[the_form].elements['selid[]']) != 'undefined')
                  ? document.forms[the_form].elements['selid[]']
                  : (typeof(document.forms[the_form].elements['selid[]']) != 'undefined')
          ? document.forms[the_form].elements['selid[]']
          : document.forms[the_form].elements['selid[]'];
    var elts2 = (typeof(document.forms[the_form].elements['selid2[]']) != 'undefined')
                  ? document.forms[the_form].elements['selid2[]']
                  : (typeof(document.forms[the_form].elements['selid2[]']) != 'undefined')
          ? document.forms[the_form].elements['selid2[]']
          : document.forms[the_form].elements['selid2[]'];

    if (elts) {
		var elts_cnt  = (typeof(elts.length) != 'undefined')
                  ? elts.length
                  : 0;
		if (elts_cnt) {
			for (var i = 0; i < elts_cnt; i++) {
				elts[i].checked = do_check;
			} 
		} else {
			elts.checked  = do_check;
		}
	}
    if (elts2) {
		var elts_cnt2  = (typeof(elts2.length) != 'undefined')
                  ? elts2.length
                  : 0;
		if (elts_cnt2) {
			for (var i = 0; i < elts_cnt2; i++) {
				elts2[i].checked = do_check;
			} 
		} else {
			elts2.checked  = do_check;
		} 
	}
	return true;
}

function ensuredel (blogid, property) {
	if (property==1) 	{
		var urlreturn="admin.php?go=entry_deleteblog_"+blogid+'&returnurl='+escape('admin.php?go=entry_default');
	} else if (property==2)  {
		var urlreturn="admin.php?go=reply_delreply_"+blogid+'&returnurl='+escape('admin.php?go=reply_default');
	} else if (property==3)  {
		var urlreturn="admin.php?go=message_delreply_"+blogid+'&returnurl='+escape('admin.php?go=message_default');
	}  else if (property==4)  {
		var urlreturn="admin.php?go=reply_delreply_"+blogid+'&returnurl='+escape('admin.php?go=reply_censor');
	}  else if (property==5)  {
		var urlreturn="admin.php?go=message_delreply_"+blogid+'&returnurl='+escape('admin.php?go=message_censor');
	} else {
		var urlreturn="admin.php?go=entry_deletedraft_"+blogid+'&returnurl='+escape('admin.php?go=entry_draft');
	}
	if(confirm(jslang[16])){
		if (shutajax==0) {
			makeRequest(urlreturn+"&ajax=on", 'adminSubmitAjaxRun', 'GET', null);
		}
		else {
			window.location=urlreturn;
		}
	}
	else {
		return;
	}
}

function redirectcomfirm (returnurl) {
	if(confirm(jslang[5])){
		if (shutajax==0) {
			makeRequest(returnurl+"&ajax=on", 'adminSubmitAjaxRun', 'GET', null);
		}
		else {
			window.location=returnurl;
		}
	}
	else {
		return;
	}
}

function inserttag (realvalue, taginputname) {
	var targetinput=document.getElementById(taginputname);
	if (targetinput && realvalue!='' && realvalue!=null) {
		if (targetinput.value=='') var newvalue=realvalue;
		else var newvalue=' '+realvalue;
		targetinput.value+=newvalue;
	}
}

function makesuredelweather(weathername) {
	var urlreturn="admin.php?go=misc_weatherdel_"+weathername+'';
	if(confirm(jslang[17])){
		simulateFormSubmit(urlreturn);
	}
	else {
		return;
	}
}

function timechanger() {
	if (document.getElementById('changemytime').checked) document.getElementById('changetime').style.display='block';
	else document.getElementById('changetime').style.display='none';
}

function ajax_addcategory () {
	if (shutajax==0) {
		var newcatename=blogencode(document.getElementById('newcatename').value);
		var newcatedesc=blogencode(document.getElementById('newcatedesc').value);
		var seld=document.getElementById('newcatemode');
		var newcatemode=blogencode(seld.options[seld.selectedIndex].value);
		seld=document.getElementById('newcateproperty');
		var newcateproperty=blogencode(seld.options[seld.selectedIndex].value);
		seld=document.getElementById('targetcate');
		var targetcate=blogencode(seld.options[seld.selectedIndex].value);
		var postData = "unuse=unuse&newcatename="+newcatename+"&newcatedesc="+newcatedesc+"&newcatemode="+newcatemode+"&newcateproperty="+newcateproperty+"&targetcate="+targetcate;
		var gourl="admin.php?ajax=on&go=category_newinedit";
		makeRequest(gourl, 'quickaddcategory', 'POST', postData);
	}
}

function changeeditor() {
	if(confirm(jslang[68])){
		var oldgo=document.getElementById('oldgo').value;
		var editorbody=document.getElementById('useeditor');
		var useeditor=editorbody.options[editorbody.selectedIndex].value;
		window.location="admin.php?"+oldgo+"&useeditor="+useeditor;
	}
	else {
		return;
	}
}

function swapdiv(opt) {
	if (opt==0) {
		document.getElementById("targetdiv").style.display="none";
		document.getElementById("targetdiv2").style.display="none";
		document.getElementById("targetdiv3").style.display="block";
	} else if (opt==1) {
		document.getElementById("targetdiv").style.display="block";
		document.getElementById("targetdiv2").style.display="none";
		document.getElementById("targetdiv3").style.display="none";
	}  else {
		document.getElementById("targetdiv").style.display="none";
		document.getElementById("targetdiv2").style.display="block";
		document.getElementById("targetdiv3").style.display="none";
	} 
}

function clearAllRadios() {
	var a1=document.getElementsByTagName("input");
	for(var i=0;i<a1.length;i++){
		if (a1[i].type=='radio' || a1[i].type=='checkbox') {
			if (a1[i].checked) {
				a1[i].checked='';
			}
		}
	}
}

function simulateFormSubmit(url) {
	if (shutajax==0) {
		makeRequest(url+'&ajax=on', 'adminSubmitAjaxRun', 'GET', null);
	} else {
		window.location=url;
	}
}

function adminSubmitAjax(formSeq) {
	var ajaxFormName=(formSeq=='f_s') ? 'f_s' : 'ajaxForm'+formSeq;
	var ajaxForm=document.getElementById(ajaxFormName);
	if (shutajax==0) {
		var str='';
		var a=document.getElementsByTagName("input");
		for(var i=0;i<a.length;i++){
			/*
			if (a[i].type=='file') {
				ajaxForm.submit();
				return false;
			}
			*/
			if (a[i].type=='text' || a[i].type=='hidden' || a[i].type=='password') {
				str+=a[i].name+'='+blogencode(a[i].value)+"&";
			}
			if (a[i].type=='radio' || a[i].type=='checkbox') {
				if (a[i].checked) {
					str+=a[i].name+'='+blogencode(a[i].value)+"&";
				}
			}
		}
		var a=document.getElementsByTagName("select");
		
		for(var i=0;i<a.length;i++){
			var selectedIndex=a[i].selectedIndex;
			//alert(selectedIndex);
			if(selectedIndex!='-1') str+=a[i].name+'='+blogencode(a[i].options[selectedIndex].value)+"&";
		}
		var a=document.getElementsByTagName("textarea");
		for(var i=0;i<a.length;i++){
			str+=a[i].name+'='+blogencode(a[i].value)+"&";
		}
		adminAjaxLoading();
		ajaxURL=(ajaxForm.action.indexOf("?")!=-1) ? ajaxForm.action+"&ajax=on" : ajaxForm.action+"?ajax=on";
		makeRequest(ajaxURL, 'adminSubmitAjaxRun', 'POST', str);
	}
	else {
		ajaxForm.submit();
	}
}

var fadingcount=1.0;

function adminAjaxAlert(ajaxMsg, ajaxStatus) {
	var statusBox=(ajaxStatus==0) ? 'success' : 'failure';
	var changeBoxText="<div id='changeBox' class='adminbox-"+statusBox+"'>"+ajaxMsg+"</div><div id='divMask' class='adminbox-"+statusBox+"-mask'></div>";
	document.getElementById('adminalert').style.display='block';
	document.getElementById('adminalert').innerHTML=changeBoxText;
	window.location.hash="beginning";
	fadingcount=1.0;
	fadingMaskDo();
}

function fadingMaskInit() {
	fadingcount=fadingcount-0.1;
	if (fadingcount>0) {
		if (document.getElementById('divMask')) {
			document.getElementById('divMask').style.filter="progid:DXImageTransform.Microsoft.BasicImage(opacity= "+fadingcount+")";
			document.getElementById('divMask').style.opacity=fadingcount;
		}
	}
	if (fadingcount<-1.5) {
		document.getElementById('adminalert').innerHTML='';
		document.getElementById('adminalert').style.display='none';
		fadingcount=1.0;
	}
	else {
		fadingMaskDo();
	}
}

function fadingMaskDo() {
	window.setTimeout("fadingMaskInit()",150);
}

function adminAjaxLoading() {
	var changeBoxText="<div id='changeBox' class='adminbox-loading'><img align='absmiddle' src='"+ajaxloadingIMG+"'/> Loading...</div>";
	document.getElementById('adminalert').style.display='block';
	document.getElementById('adminalert').innerHTML=changeBoxText;
	window.location.hash="beginning";
}
