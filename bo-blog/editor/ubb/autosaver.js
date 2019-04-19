/*    本文件部分思路和方法来自
       基于Ajax的网站通用草稿自动保存系统SipoAutoSaver(ver 3.0)  by Sipo
       http://www.dc9.cn/post/SipoAutoSaverV3.html                                 */

var AutoSaveTime=60;   //修改每次保存时间(秒)
var AutoHideMsg=55;  

var autosaveroff=getCookie ('autosaveroff');
if (autosaveroff!=1) {
	savertimer = window.setTimeout("timer()", 0);
}
savetime=AutoSaveTime;
function timer() { 
	var timemsg=document.getElementById('timemsg');
	var timemsg2=document.getElementById('timemsg2');
	savetime=savetime-1;
	timemsg.innerHTML = savetime+jslang[63];
	if (savetime>=0){
		savertimer = window.setTimeout("timer()", 1000);
		if (savetime==AutoHideMsg) timemsg2.innerHTML='';
	}
	else {
		if (savetime<=-1000) {savetime=AutoSaveTime;timer();}
		else{
			timemsg.innerHTML = jslang[64];
			savedraft();
			savetime=AutoSaveTime;
			timer();
		}
	}
}

function savedraft() {
	var content = blogencode(document.getElementById('content').value);
	var idforsave = blogencode(document.getElementById('idforsave').value);
	var title = blogencode(document.getElementById('title').value);
	var category=document.getElementById('category').options[document.getElementById('category').selectedIndex].value;	
//	var tags = blogencode(document.getElementById('tags').value);
 	var html = document.getElementById('html').checked ? 1 : 0;
	var ubb = document.getElementById('ubb').checked ? 1 : 0;
	var emot = document.getElementById('emot').checked ? 1 : 0;
	var property = document.getElementById('property').checked ? 1 : 0;
	var starred =(document.getElementById('starred')) ? (document.getElementById('starred').checked ? 1 : 0) : '';
	var sweather=(document.getElementById('sweather')) ? document.getElementById('sweather').options[document.getElementById('sweather').selectedIndex].value : '';	
//	var blogalias = blogencode(document.getElementById('blogalias').value);
	var originsrc = blogencode(document.getElementById('originsrc').value);
	var comefrom = blogencode(document.getElementById('comefrom').value);
	var entrysummary = blogencode(document.getElementById('entrysummary').value);
	var summaryway=document.getElementById('summaryway').options[document.getElementById('summaryway').selectedIndex].value;	
	var thego = blogencode(document.getElementById('go').value);
	var useeditor = blogencode(document.getElementById('useeditor').options[document.getElementById('useeditor').selectedIndex].value);

	var gourl="admin.php";
	var postData = "unuse=unuse&go="+thego+"&ajax=on&useeditor="+useeditor+"&title="+title+"&content="+content+"&idforsave="+idforsave+"&category="+category+"&html="+html+"&ubb="+ubb+"&emot="+emot+"&property="+property+"&sweather="+sweather+"&originsrc="+originsrc+"&comefrom="+comefrom+"&summaryway="+summaryway+"&entrysummary="+entrysummary;
	makeRequest(gourl, 'savemydraft', 'POST', postData);
}

function cleardraft() {
	var gourl="admin.php";
	var postData = "unuse=unuse&go=entry_deletedraft_-1&ajax=on";
	makeRequest(gourl, 'clearmydraft', 'POST', postData);
}

function savemydraft() {
	var timemsg2=document.getElementById('timemsg2');
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			var messagereturn = http_request.responseText;
			if (messagereturn.indexOf("<boblog_ajax::error>")!=-1) {
				messagereturn=messagereturn.replace("<boblog_ajax::error>", '');
				alert(jslang[74]+jslang[75]+messagereturn);
				stopautosaver();
			} else if (messagereturn.indexOf("<boblog_ajax::success>")!=-1) {
				messagereturn=messagereturn.replace("<boblog_ajax::success>", '');
				timemsg2.innerHTML = jslang[65];
				savetime=-1000;
			} else {
				alert(jslang[74]+jslang[75]+messagereturn);
				stopautosaver();
			}
		}  else {
			alert(jslang[74]);
			stopautosaver();
		}
	}
}

function clearmydraft() {
	var timemsg2=document.getElementById('timemsg2');
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			var messagereturn = http_request.responseText;
			if (messagereturn.indexOf("<boblog_ajax::error>")!=-1) {
				alert(jslang[77]+jslang[75]+messagereturn);
				stopautosaver();
			} else if (messagereturn.indexOf("<boblog_ajax::success>")!=-1) {
			} else {
				alert(jslang[77]+jslang[75]+messagereturn);
				stopautosaver();
			}
		}  else {
			alert(jslang[77]);
			stopautosaver();
		}
	}
}


function stopautosaver () {
	clearTimeout(savertimer);
}

function restartautosaver () {
	savertimer = window.setTimeout("timer()", 0);
	var dateObjexp= new Date();
	dateObjexp.setFullYear(2010); 
	setCookie('autosaveroff', 0,dateObjexp, null, null, false);
}

function stopforever() {
	var dateObjexp= new Date();
	dateObjexp.setFullYear(2010); 
	setCookie('autosaveroff', 1,dateObjexp, null, null, false);
	stopautosaver();
	document.getElementById('timemsg').innerHTML=jslang[73];
}

function switchtodraft() {
	if(confirm(jslang[76])){
		window.location="admin.php?go=edit_edit_-1&ignore=1";
	}
	else {
		return;
	}
}