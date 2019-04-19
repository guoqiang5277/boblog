var tb;
var tbshow;

function simple_ac_do() {
	if (tb.value.charAt(tb.value.length-1)==' ') {
		tbshow.innerHTML='';
		return;
	}
	var tbvs=tb.value.Trim();
	if (tbvs.length==0) {
		tbshow.innerHTML='';
	}
	else {
		var tbv;
		if (tbvs.indexOf(' ')==-1) {
			tbv=tbvs;
		} else {
			var tbvcluster=tbvs.split(' ');
			var tbvcluster_last=tbvcluster.length-1;
			tbv=tbvcluster[tbvcluster_last];
		}

		//alert(tbv);
		var tbpattern=new RegExp("^"+tbv, "i");
		var tbshowresult='';
		var tbresult;

		for (var i=0; i<custom_array.length; i++) {
			tbresult=custom_array[i];
			if (tbresult.search(tbpattern)!=-1) {
				tbshowresult+="<li><a href='javascript: simple_ac_insert(\""+tb.id+"\", \""+tbresult+"\", \""+tbshow.id+"\")'>"+tbresult.replace(tbpattern, "<b>"+tbpattern.source.substr(1)+"</b>");+"</a></li>";
			}
		}
		tbshow.innerHTML="<ul>"+tbshowresult+"</ul>";
	}
}

function simple_ac_insert (tbid, strvalue, clearfield) {
	tb=document.getElementById(tbid);
	tbvs=tb.value.Trim();
	var addstrvalue;
	if (tbvs.indexOf(' ')==-1) {
		addstrvalue=strvalue;
	} else {
		var tbvcluster=tbvs.split(' ');
		var tbvcluster_last=tbvcluster.length-1;
		tbvcluster[tbvcluster_last]=strvalue;
		addstrvalue=tbvcluster.join(' ');
	}
	tb.value=addstrvalue;
	tb.value+=' ';
	document.getElementById(clearfield).innerHTML='';
	tb.focus();
}

function simple_ac_init(tbid, tbshowid) {
	tb=document.getElementById(tbid);
	tbshow=document.getElementById(tbshowid);
	addEvent(document, 'keyup', simple_ac_do);
}

function addEvent(obj, event_name, func_ref) {
	if(obj.addEventListener && !window.opera)	{
		obj.addEventListener(event_name, func_ref, false);
	}
	else {
		obj["on" + event_name] = func_ref;
	}
}


String.prototype.Trim = function() {return this.replace(/(^\s*)|(\s*$)/g, "");}
String.prototype.LTrim = function() {return this.replace(/(^\s*)/g, "");}
String.prototype.Rtrim = function() {return this.replace(/(\s*$)/g, "");}
