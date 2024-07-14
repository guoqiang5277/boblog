var currentpicname;
var realpath="attachment/";

function insertmyUpload (filename) {
	filename=filename.replace(/\*/g, '.');
	filename=realpath+filename;
	if (document.getElementById('ifautoaddubb').checked) filename=autoattachubb (filename);
	parent.AddText(filename);
}

function generateUpload (attachid, filename) {
	filename=filename.replace(/\*/g, '.');
	filename=realpath+filename;
	if (document.getElementById('ifautoaddubb').checked) filename=autoattachubb (attachid, filename);
	parent.AddText(filename);
}

function autoattachubb (attachid, filename) {
	var finalresult;
	finalresult="[file]"+attachid+"[/file]";
	var extindex = filename.lastIndexOf(".");
	if (extindex!=-1) {
		var realext=filename.substring(extindex+1).toLowerCase();
		if (realext=="gif" || realext=="jpg" || realext=="png" || realext=="bmp" || realext=="jpeg") {
			 finalresult="[img]"+attachid+"[/img]";
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


function doinsertimage(attachid) {
	var alignpro=document.getElementById('picalign').value;
	var plusalign;
	if (alignpro=='l') plusalign=' align=l';
	else if  (alignpro=='r') plusalign=' align=r';
	else plusalign='';
	var finalresult="[img"+plusalign+"]"+attachid+"[/img]";
	parent.AddText(finalresult);
}
